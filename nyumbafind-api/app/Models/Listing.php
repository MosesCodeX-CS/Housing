<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'estate_id',
        'title',
        'description',
        'type',
        'price',
        'deposit',
        'street',
        'amenities',
        'caretaker_name',
        'caretaker_phone',
        'caretaker_whatsapp',
        'latitude',
        'longitude',
        'status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'is_featured',
        'featured_until',
        'vacancy_confirmed_at',
    ];

    protected $casts = [
        'amenities'              => 'array',
        'is_featured'            => 'boolean',
        'verified_at'            => 'datetime',
        'featured_until'         => 'datetime',
        'vacancy_confirmed_at'   => 'datetime',
        'price'                  => 'integer',
        'deposit'                => 'integer',
        'latitude'               => 'float',
        'longitude'              => 'float',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function media()
    {
        return $this->hasMany(ListingMedia::class)->orderBy('order');
    }

    public function photos()
    {
        return $this->hasMany(ListingMedia::class)->where('type', 'photo')->orderBy('order');
    }

    public function videos()
    {
        return $this->hasMany(ListingMedia::class)->where('type', 'video');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(ListingMedia::class)->where('is_primary', true)->where('type', 'photo');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function verificationLogs()
    {
        return $this->hasMany(VerificationLog::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVacant($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->where(function ($q) {
                         $q->whereNull('featured_until')
                           ->orWhere('featured_until', '>', now());
                     });
    }

    public function scopeInEstate($query, int $estateId)
    {
        return $query->where('estate_id', $estateId);
    }

    public function scopePriceBetween($query, ?int $min, ?int $max)
    {
        if ($min) $query->where('price', '>=', $min);
        if ($max) $query->where('price', '<=', $max);
        return $query;
    }

    // ─── Helpers ───────────────────────────────────────────────

    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }

    public function isVacant(): bool
    {
        return $this->status === 'active';
    }

    public function whatsappLink(): string
    {
        $phone = ltrim($this->caretaker_whatsapp ?? $this->caretaker_phone, '+');
        $message = urlencode("Hi, I saw your listing on NyumbaFind: {$this->title}. Is it still available?");
        return "https://wa.me/{$phone}?text={$message}";
    }

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
