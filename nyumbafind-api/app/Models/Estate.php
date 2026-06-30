<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    protected $fillable = [
        'name', 'slug', 'county', 'sub_county', 'ward',
        'latitude', 'longitude', 'description', 'is_active', 'listing_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function activeListings()
    {
        return $this->hasMany(Listing::class)->where('status', 'active');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
