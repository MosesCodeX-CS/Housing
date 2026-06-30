<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\Listing;
use App\Models\ListingMedia;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListingController extends Controller
{
    public function __construct(private MediaService $media) {}

    // ─── PUBLIC: Search & Browse ───────────────────────────────
    // GET /api/listings
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'estate'    => ['sometimes', 'string'],         // estate slug
            'type'      => ['sometimes', 'in:bedsitter,1br,2br,3br,single_room,studio'],
            'min_price' => ['sometimes', 'integer', 'min:0'],
            'max_price' => ['sometimes', 'integer', 'min:0'],
            'amenities' => ['sometimes', 'array'],          // ['wifi','water','parking']
            'sort'      => ['sometimes', 'in:newest,price_asc,price_desc,popular'],
            'per_page'  => ['sometimes', 'integer', 'min:5', 'max:50'],
        ]);

        $query = Listing::with(['estate', 'primaryPhoto', 'verifiedBy'])
            ->active();

        // Estate filter
        if ($request->filled('estate')) {
            $estate = Estate::where('slug', $request->estate)->firstOrFail();
            $query->where('estate_id', $estate->id);
        }

        // House type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Price range
        $query->priceBetween($request->min_price, $request->max_price);

        // Amenities filter (must have ALL requested amenities)
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

        // Featured listings float to top
        $query->orderBy('is_featured', 'desc');

        $listings = $query->paginate($request->per_page ?? 15);

        return response()->json($listings);
    }

    // ─── PUBLIC: Show single listing ───────────────────────────
    // GET /api/listings/{id}
    public function show(int $id): JsonResponse
    {
        $listing = Listing::with([
            'estate',
            'media',
            'user:id,name,phone',
            'reviews.user:id,name,avatar',
            'verifiedBy:id,name',
        ])->active()->findOrFail($id);

        $listing->incrementViews();

        return response()->json([
            'listing'        => $listing,
            'whatsapp_link'  => $listing->whatsappLink(),
            'average_rating' => $listing->averageRating(),
        ]);
    }

    // ─── LANDLORD: Create listing ──────────────────────────────
    // POST /api/listings
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'estate_id'          => ['nullable', 'exists:estates,id'],
            'estate_name'        => ['required_without:estate_id', 'string', 'max:100'],
            'county'             => ['required_without:estate_id', 'string', 'max:100'],
            'sub_county'         => ['required_without:estate_id', 'string', 'max:100'],
            'ward'               => ['required_without:estate_id', 'string', 'max:100'],
            'title'              => ['required', 'string', 'max:150'],
            'description'        => ['nullable', 'string', 'max:2000'],
            'type'               => ['required', 'in:bedsitter,1br,2br,3br,single_room,studio'],
            'price'              => ['required', 'integer', 'min:500'],
            'deposit'            => ['nullable', 'integer', 'min:0'],
            'street'             => ['nullable', 'string', 'max:200'],
            'caretaker_name'     => ['nullable', 'string', 'max:100'],
            'caretaker_phone'    => ['required', 'string'],
            'caretaker_whatsapp' => ['nullable', 'string'],
            'latitude'           => ['nullable', 'numeric'],
            'longitude'          => ['nullable', 'numeric'],
            'amenities'          => ['nullable', 'array'],
            'amenities.*'        => ['in:water,electricity,wifi,parking,security,borehole,cctv,gym,pool'],
            'photos'             => ['required', 'array', 'min:1', 'max:10'],
            'photos.*'           => ['image', 'max:5120'],         // max 5MB each
            'videos'             => ['nullable', 'array', 'max:2'],
            'videos.*'           => ['mimetypes:video/mp4,video/quicktime', 'max:51200'], // max 50MB
        ]);

        DB::beginTransaction();

        try {
            $estateId = $request->estate_id;

            // Resolve or create estate dynamically if a custom location is provided
            if (!$estateId && $request->filled('estate_name')) {
                $slug = \Illuminate\Support\Str::slug($request->estate_name);
                
                // Ensure unique slug or append a random element if already exists
                $count = Estate::where('slug', $slug)->count();
                if ($count > 0) {
                    $slug .= '-' . rand(100, 999);
                }

                $estate = Estate::create([
                    'name'          => $request->estate_name,
                    'slug'          => $slug,
                    'county'        => $request->county,
                    'sub_county'    => $request->sub_county,
                    'ward'          => $request->ward,
                    'latitude'      => $request->latitude ?? -1.2921, // default Nairobi center if empty
                    'longitude'     => $request->longitude ?? 36.8219,
                    'is_active'     => true,
                    'listing_count' => 0,
                ]);
                $estateId = $estate->id;
            }

            $listing = Listing::create([
                ...$request->only([
                    'title', 'description', 'type', 'price', 'deposit',
                    'street', 'caretaker_name', 'caretaker_phone', 'caretaker_whatsapp',
                    'latitude', 'longitude',
                ]),
                'estate_id' => $estateId,
                'user_id'   => $request->user()->id,
                'status'    => 'pending',           // Awaits admin verification
                'amenities' => $this->buildAmenities($request->amenities ?? []),
            ]);

            // Upload photos
            foreach ($request->file('photos') as $index => $photo) {
                $uploaded = $this->media->uploadPhoto($photo, $listing->id);
                ListingMedia::create([
                    'listing_id'  => $listing->id,
                    'type'        => 'photo',
                    'url'         => $uploaded['url'],
                    'public_id'   => $uploaded['public_id'],
                    'is_primary'  => $index === 0,
                    'order'       => $index,
                    'file_size'   => $photo->getSize(),
                    'mime_type'   => $photo->getMimeType(),
                ]);
            }

            // Upload videos
            foreach ($request->file('videos', []) as $index => $video) {
                $uploaded = $this->media->uploadVideo($video, $listing->id);
                ListingMedia::create([
                    'listing_id'    => $listing->id,
                    'type'          => 'video',
                    'url'           => $uploaded['url'],
                    'thumbnail_url' => $uploaded['thumbnail'] ?? null,
                    'public_id'     => $uploaded['public_id'],
                    'order'         => $index,
                    'file_size'     => $video->getSize(),
                    'mime_type'     => $video->getMimeType(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Listing submitted for verification. You\'ll be notified once approved.',
                'listing' => $listing->load('media'),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ─── LANDLORD: Update listing ──────────────────────────────
    // PUT /api/listings/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'title'              => ['sometimes', 'string', 'max:150'],
            'description'        => ['nullable', 'string', 'max:2000'],
            'price'              => ['sometimes', 'integer', 'min:500'],
            'deposit'            => ['nullable', 'integer'],
            'street'             => ['nullable', 'string'],
            'caretaker_name'     => ['nullable', 'string'],
            'caretaker_phone'    => ['sometimes', 'string'],
            'caretaker_whatsapp' => ['nullable', 'string'],
            'amenities'          => ['nullable', 'array'],
        ]);

        // If price or key details change — re-send for verification
        $needsReverification = $request->has('price') && $request->price !== $listing->price;

        $listing->update([
            ...$request->only(['title', 'description', 'price', 'deposit', 'street',
                               'caretaker_name', 'caretaker_phone', 'caretaker_whatsapp']),
            ...$request->has('amenities') ? ['amenities' => $this->buildAmenities($request->amenities)] : [],
            ...($needsReverification ? ['status' => 'pending', 'verified_at' => null] : []),
        ]);

        return response()->json([
            'message'             => $needsReverification
                ? 'Listing updated and re-submitted for verification due to price change.'
                : 'Listing updated successfully.',
            'listing'             => $listing->fresh()->load('media'),
        ]);
    }

    // ─── LANDLORD: Mark as occupied / vacant ──────────────────
    // PATCH /api/listings/{id}/status
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:active,occupied'],
        ]);

        $listing->update([
            'status'                => $request->status,
            'vacancy_confirmed_at'  => $request->status === 'active' ? now() : $listing->vacancy_confirmed_at,
        ]);

        return response()->json([
            'message' => $request->status === 'active'
                ? 'Listing marked as vacant. Tenants can now see it.'
                : 'Listing marked as occupied.',
        ]);
    }

    // ─── LANDLORD: My listings ─────────────────────────────────
    // GET /api/my-listings
    public function myListings(Request $request): JsonResponse
    {
        $listings = Listing::with(['estate', 'primaryPhoto'])
            ->where('user_id', $request->user()->id)
            ->withCount(['inquiries', 'reviews'])
            ->latest()
            ->paginate(20);

        return response()->json($listings);
    }

    // ─── LANDLORD: Delete listing ──────────────────────────────
    // DELETE /api/listings/{id}
    public function destroy(Request $request, int $id): JsonResponse
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);
        $listing->delete();

        return response()->json(['message' => 'Listing removed.']);
    }

    // ─── Helpers ───────────────────────────────────────────────

    private function buildAmenities(array $selected): array
    {
        $all = ['water', 'electricity', 'wifi', 'parking', 'security', 'borehole', 'cctv', 'gym', 'pool'];
        return collect($all)->mapWithKeys(fn ($a) => [$a => in_array($a, $selected)])->toArray();
    }
}
