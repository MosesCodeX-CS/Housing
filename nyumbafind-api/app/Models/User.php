<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'avatar',
        'whatsapp_number',
        'is_active',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'phone_verified_at'  => 'datetime',
        'email_verified_at'  => 'datetime',
        'is_active'          => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'tenant_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    // ─── Helpers ───────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLandlord(): bool
    {
        return in_array($this->role, ['landlord', 'caretaker', 'agent']);
    }

    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    // Normalize Kenyan phone to +2547XXXXXXXX
    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        if (! str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }

        return '+' . $phone;
    }
}
