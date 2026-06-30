<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'listing_id', 'tenant_id', 'message', 'status',
        'whatsapp_opened_at', 'responded_at',
    ];

    protected $casts = [
        'whatsapp_opened_at' => 'datetime',
        'responded_at'       => 'datetime',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
