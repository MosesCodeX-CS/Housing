<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use Illuminate\Http\JsonResponse;

class EstateController extends Controller
{
    // GET /api/estates
    public function index(): JsonResponse
    {
        $estates = Estate::where('is_active', true)
            ->orderBy('listing_count', 'desc')
            ->get(['id', 'name', 'slug', 'county', 'sub_county', 'latitude', 'longitude', 'listing_count']);

        return response()->json($estates);
    }

    // GET /api/estates/{slug}
    public function show(string $slug): JsonResponse
    {
        $estate = Estate::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json([
            'estate'          => $estate,
            'listing_count'   => $estate->activeListings()->count(),
        ]);
    }
}
