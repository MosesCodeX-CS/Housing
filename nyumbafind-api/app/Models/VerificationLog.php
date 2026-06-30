<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationLog extends Model
{
    protected $fillable = [
        'listing_id', 'admin_id', 'action', 'notes',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
