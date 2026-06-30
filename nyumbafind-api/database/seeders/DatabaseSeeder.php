<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin user ───────────────────────────────────────────
        User::firstOrCreate(
            ['phone' => '+254700000000'],
            [
                'name'             => 'NyumbaFind Admin',
                'email'            => 'admin@nyumbafind.co.ke',
                'password'         => Hash::make('NyumbaAdmin@2024'),
                'role'             => 'admin',
                'is_active'        => true,
                'phone_verified_at' => now(),
            ]
        );

        // ─── Estates ──────────────────────────────────────────────
        $this->call(EstateSeeder::class);

        // ─── Sample Listings ──────────────────────────────────────
        $this->call(ListingSeeder::class);
    }
}
