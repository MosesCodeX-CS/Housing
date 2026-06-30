<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingMedia extends Model
{
    protected $table = 'listing_media';

    protected $fillable = [
        'listing_id', 'type', 'url', 'thumbnail_url',
        'public_id', 'is_primary', 'order', 'file_size', 'mime_type',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order'      => 'integer',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
