<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    // POST /api/listings/{id}/inquire
    public function store(Request $request, int $listingId): JsonResponse
    {
        $listing = Listing::active()->findOrFail($listingId);

        $existing = Inquiry::where('listing_id', $listingId)
            ->where('tenant_id', $request->user()->id)
            ->where('status', '!=', 'closed')
            ->first();

        if ($existing) {
            return response()->json([
                'message'       => 'You already have an open inquiry for this listing.',
                'whatsapp_link' => $listing->whatsappLink(),
            ]);
        }

        $inquiry = Inquiry::create([
            'listing_id' => $listingId,
            'tenant_id'  => $request->user()->id,
            'message'    => $request->message,
            'status'     => 'new',
        ]);

        return response()->json([
            'message'       => 'Inquiry recorded.',
            'inquiry_id'    => $inquiry->id,
            'whatsapp_link' => $listing->whatsappLink(),
        ], 201);
    }

    // PATCH /api/inquiries/{id}/whatsapp-opened
    public function markWhatsappOpened(Request $request, int $id): JsonResponse
    {
        $inquiry = Inquiry::where('tenant_id', $request->user()->id)->findOrFail($id);

        $inquiry->update([
            'status'             => 'whatsapp_opened',
            'whatsapp_opened_at' => now(),
        ]);

        return response()->json(['message' => 'Recorded.']);
    }

    // GET /api/my-inquiries
    public function myInquiries(Request $request): JsonResponse
    {
        $inquiries = Inquiry::with(['listing:id,title,price,type,status', 'listing.primaryPhoto'])
            ->where('tenant_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($inquiries);
    }
}
