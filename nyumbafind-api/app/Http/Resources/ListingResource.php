<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'description'          => $this->description,
            'type'                 => $this->type,
            'price'                => $this->price,
            'deposit'              => $this->deposit,
            'street'               => $this->street,
            'amenities'            => $this->amenities,
            'status'               => $this->status,
            'is_featured'          => $this->is_featured,
            'is_verified'          => $this->isVerified(),
            'verified_at'          => $this->verified_at?->toDateTimeString(),
            'vacancy_confirmed_at' => $this->vacancy_confirmed_at?->diffForHumans(),
            'views_count'          => $this->views_count,
            'whatsapp_link'        => $this->whatsappLink(),
            'average_rating'       => $this->whenLoaded('reviews', fn() => $this->averageRating()),
            'reviews_count'        => $this->when(isset($this->reviews_count), $this->reviews_count),
            'inquiries_count'      => $this->when(isset($this->inquiries_count), $this->inquiries_count),
            'caretaker_name'       => $this->caretaker_name,
            'caretaker_phone'      => $this->caretaker_phone,
            'caretaker_whatsapp'   => $this->caretaker_whatsapp,
            'latitude'             => $this->latitude,
            'longitude'            => $this->longitude,
            'created_at'           => $this->created_at?->toDateString(),

            // Nested
            'estate'         => $this->whenLoaded('estate', fn() => [
                'id'     => $this->estate->id,
                'name'   => $this->estate->name,
                'slug'   => $this->estate->slug,
                'county' => $this->estate->county,
            ]),
            'media'          => $this->whenLoaded('media', fn() => $this->media->map(fn($m) => [
                'id'            => $m->id,
                'type'          => $m->type,
                'url'           => $m->url,
                'thumbnail_url' => $m->thumbnail_url,
                'is_primary'    => $m->is_primary,
                'order'         => $m->order,
            ])),
            'primary_photo'  => $this->whenLoaded('primaryPhoto', fn() => $this->primaryPhoto?->url),
            'reviews'        => $this->whenLoaded('reviews', fn() => $this->reviews->map(fn($r) => [
                'id'         => $r->id,
                'rating'     => $r->rating,
                'comment'    => $r->comment,
                'created_at' => $r->created_at?->toDateString(),
                'user'       => ['name' => $r->user?->name, 'avatar' => $r->user?->avatar],
            ])),
            'owner'          => $this->whenLoaded('user', fn() => [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
            ]),
        ];
    }
}
