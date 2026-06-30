<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // POST /api/listings/{id}/report
    public function store(Request $request, int $listingId): JsonResponse
    {
        $request->validate([
            'reason'  => ['required', 'in:fake_listing,wrong_price,already_occupied,wrong_location,scam,inappropriate_content,other'],
            'details' => ['nullable', 'string', 'max:500'],
        ]);

        Listing::findOrFail($listingId);

        $existing = Report::where('listing_id', $listingId)
            ->where('reporter_id', $request->user()->id)
            ->whereIn('status', ['open', 'under_review'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You have already reported this listing. Our team is reviewing it.',
            ], 409);
        }

        Report::create([
            'listing_id'  => $listingId,
            'reporter_id' => $request->user()->id,
            'reason'      => $request->reason,
            'details'     => $request->details,
            'status'      => 'open',
        ]);

        $openReports = Report::where('listing_id', $listingId)
            ->where('status', 'open')
            ->count();

        if ($openReports >= 5) {
            // TODO: send urgent admin alert when 5+ reports on same listing
        }

        return response()->json([
            'message' => "Thank you. We'll review this listing within 24 hours.",
        ], 201);
    }
}
