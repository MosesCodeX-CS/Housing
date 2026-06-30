<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // POST /api/listings/{id}/reviews
    public function store(Request $request, int $listingId): JsonResponse
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Listing::findOrFail($listingId);

        $review = Review::updateOrCreate(
            ['listing_id' => $listingId, 'user_id' => $request->user()->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return response()->json([
            'message' => 'Review saved.',
            'review'  => $review,
        ], 201);
    }

    // PUT /api/listings/{id}/reviews
    public function update(Request $request, int $listingId): JsonResponse
    {
        return $this->store($request, $listingId);
    }
}
