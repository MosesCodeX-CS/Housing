<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Estate;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Full-text listing search.
     * GET /api/search?q=bedsitter&estate=rongai&type=bedsitter&min_price=5000&max_price=15000
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q'          => ['sometimes', 'string', 'max:100'],
            'estate'     => ['sometimes', 'string'],
            'county'     => ['sometimes', 'string'],
            'sub_county' => ['sometimes', 'string'],
            'ward'       => ['sometimes', 'string'],
            'type'       => ['sometimes', 'in:bedsitter,1br,2br,3br,single_room,studio'],
            'min_price'  => ['sometimes', 'integer', 'min:0'],
            'max_price'  => ['sometimes', 'integer', 'min:0'],
            'amenities'  => ['sometimes', 'array'],
            'sort'       => ['sometimes', 'in:newest,price_asc,price_desc,popular'],
            'per_page'   => ['sometimes', 'integer', 'min:5', 'max:50'],
        ]);

        $query = Listing::with(['estate', 'primaryPhoto'])
            ->active();

        // Keyword search across title, description, street
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'ilike', "%{$q}%")
                    ->orWhere('description', 'ilike', "%{$q}%")
                    ->orWhere('street', 'ilike', "%{$q}%");
            });
        }

        // Estate filter
        if ($request->filled('estate')) {
            $estate = Estate::where('slug', $request->estate)->first();
            if ($estate) {
                $query->where('estate_id', $estate->id);
            }
        }

        // County filter
        if ($request->filled('county')) {
            $query->whereHas('estate', function ($q) use ($request) {
                $q->where('county', 'ilike', $request->county);
            });
        }

        // Sub-county filter
        if ($request->filled('sub_county')) {
            $query->whereHas('estate', function ($q) use ($request) {
                $q->where('sub_county', 'ilike', $request->sub_county);
            });
        }

        // Ward filter
        if ($request->filled('ward')) {
            $query->whereHas('estate', function ($q) use ($request) {
                $q->where('ward', 'ilike', $request->ward);
            });
        }

        // House type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Price range
        $query->priceBetween($request->min_price, $request->max_price);

        // Amenities filter
        if ($request->filled('amenities')) {
            foreach ($request->amenities as $amenity) {
                $query->whereJsonContains('amenities->' . $amenity, true);
            }
        }

        // Sort
        match ($request->sort ?? 'newest') {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular'    => $query->orderBy('views_count', 'desc'),
            default      => $query->latest(),
        };

        // Featured float to top
        $query->orderBy('is_featured', 'desc');

        $results = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'data'       => ListingResource::collection($results->items()),
            'total'      => $results->total(),
            'per_page'   => $results->perPage(),
            'current_page' => $results->currentPage(),
            'last_page'  => $results->lastPage(),
            'query'      => $request->only('q', 'estate', 'type', 'min_price', 'max_price'),
        ]);
    }
}
