<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use App\Models\VerificationLog;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ─── Pending listings queue ────────────────────────────────
    // GET /api/admin/listings/pending
    public function pendingListings(Request $request): JsonResponse
    {
        $listings = Listing::with(['user:id,name,phone', 'estate', 'media'])
            ->where('status', 'pending')
            ->withCount('reports')
            ->oldest()  // FIFO — oldest submissions first
            ->paginate(20);

        return response()->json($listings);
    }

    // ─── All listings (with filters) ──────────────────────────
    // GET /api/admin/listings
    public function allListings(Request $request): JsonResponse
    {
        $request->validate([
            'status'    => ['sometimes', 'in:draft,pending,active,occupied,suspended'],
            'estate_id' => ['sometimes', 'integer'],
            'search'    => ['sometimes', 'string', 'max:100'],
        ]);

        $query = Listing::with(['user:id,name,phone', 'estate', 'primaryPhoto'])
            ->withCount(['reports', 'inquiries']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('estate_id')) {
            $query->where('estate_id', $request->estate_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('caretaker_phone', 'like', "%{$request->search}%");
            });
        }

        return response()->json($query->latest()->paginate(25));
    }

    // ─── Approve a listing ────────────────────────────────────
    // POST /api/admin/listings/{id}/approve
    public function approve(Request $request, int $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);

        $listing->update([
            'status'      => 'active',
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
            'rejection_reason' => null,
            'vacancy_confirmed_at' => now(),
        ]);

        VerificationLog::create([
            'listing_id' => $listing->id,
            'admin_id'   => $request->user()->id,
            'action'     => 'approved',
            'notes'      => $request->notes,
        ]);

        // Notify landlord via SMS
        try {
            app(SmsService::class)->send(
                $listing->user->phone,
                "NyumbaFind: Your listing '{$listing->title}' has been verified and is now live! Tenants can now see it."
            );
        } catch (\Throwable) { /* swallow — listing is already approved */ }

        return response()->json([
            'message' => "Listing '{$listing->title}' approved and is now live.",
        ]);
    }

    // ─── Reject a listing ────────────────────────────────────
    // POST /api/admin/listings/{id}/reject
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $listing = Listing::findOrFail($id);

        $listing->update([
            'status'           => 'draft',
            'rejection_reason' => $request->reason,
            'verified_at'      => null,
            'verified_by'      => null,
        ]);

        VerificationLog::create([
            'listing_id' => $listing->id,
            'admin_id'   => $request->user()->id,
            'action'     => 'rejected',
            'notes'      => $request->reason,
        ]);

        // Notify landlord via SMS with rejection reason
        try {
            app(SmsService::class)->send(
                $listing->user->phone,
                "NyumbaFind: Your listing '{$listing->title}' was not approved. Reason: {$request->reason}. Please update and resubmit."
            );
        } catch (\Throwable) { /* swallow */ }

        return response()->json(['message' => 'Listing rejected. Landlord has been notified.']);
    }

    // ─── Suspend a listing ───────────────────────────────────
    // POST /api/admin/listings/{id}/suspend
    public function suspend(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $listing = Listing::findOrFail($id);
        $listing->update(['status' => 'suspended']);

        VerificationLog::create([
            'listing_id' => $listing->id,
            'admin_id'   => $request->user()->id,
            'action'     => 'suspended',
            'notes'      => $request->reason,
        ]);

        return response()->json(['message' => 'Listing suspended.']);
    }

    // ─── Feature a listing ───────────────────────────────────
    // POST /api/admin/listings/{id}/feature
    public function feature(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $listing = Listing::findOrFail($id);
        $listing->update([
            'is_featured'   => true,
            'featured_until' => now()->addDays($request->days),
        ]);

        VerificationLog::create([
            'listing_id' => $listing->id,
            'admin_id'   => $request->user()->id,
            'action'     => 'featured',
            'notes'      => "Featured for {$request->days} days",
        ]);

        return response()->json(['message' => "Listing featured for {$request->days} days."]);
    }

    // ─── Open reports ────────────────────────────────────────
    // GET /api/admin/reports
    public function reports(Request $request): JsonResponse
    {
        $reports = Report::with([
            'listing:id,title,status,estate_id',
            'listing.estate:id,name',
            'reporter:id,name,phone',
        ])
        ->where('status', 'open')
        ->withCount(['listing as listing_total_reports' => function ($q) {
            $q->selectRaw('count(*)');
        }])
        ->oldest()
        ->paginate(20);

        return response()->json($reports);
    }

    // ─── Resolve a report ───────────────────────────────────
    // POST /api/admin/reports/{id}/resolve
    public function resolveReport(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'action'  => ['required', 'in:dismiss,suspend_listing,remove_listing'],
            'notes'   => ['required', 'string', 'max:500'],
        ]);

        $report = Report::with('listing')->findOrFail($id);

        $report->update([
            'status'           => $request->action === 'dismiss' ? 'dismissed' : 'resolved',
            'resolved_by'      => $request->user()->id,
            'resolution_notes' => $request->notes,
            'resolved_at'      => now(),
        ]);

        if ($request->action === 'suspend_listing') {
            $report->listing->update(['status' => 'suspended']);
        } elseif ($request->action === 'remove_listing') {
            $report->listing->delete();
        }

        return response()->json(['message' => 'Report resolved.']);
    }

    // ─── Dashboard stats ─────────────────────────────────────
    // GET /api/admin/stats
    public function stats(): JsonResponse
    {
        return response()->json([
            'listings' => [
                'pending'   => Listing::where('status', 'pending')->count(),
                'active'    => Listing::where('status', 'active')->count(),
                'occupied'  => Listing::where('status', 'occupied')->count(),
                'suspended' => Listing::where('status', 'suspended')->count(),
            ],
            'reports' => [
                'open' => Report::where('status', 'open')->count(),
            ],
            'users_total' => User::count(),
            'new_today'   => Listing::whereDate('created_at', today())->count(),
        ]);
    }

    // ─── User list ───────────────────────────────────────────
    // GET /api/admin/users
    public function users(Request $request): JsonResponse
    {
        $request->validate([
            'role'   => ['sometimes', 'in:tenant,landlord,caretaker,agent,admin'],
            'search' => ['sometimes', 'string', 'max:100'],
        ]);

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        return response()->json(
            $query->select('id', 'name', 'phone', 'email', 'role', 'is_active', 'phone_verified_at', 'created_at')
                  ->withCount(['listings', 'inquiries'])
                  ->latest()
                  ->paginate(25)
        );
    }
}
