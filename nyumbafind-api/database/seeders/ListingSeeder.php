<?php

namespace Database\Seeders;

use App\Models\Estate;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $estates = Estate::all()->keyBy('slug');

        // Create a landlord user for the listings
        $landlord = User::firstOrCreate(
            ['phone' => '+254712000001'],
            [
                'name'             => 'Demo Landlord',
                'role'             => 'landlord',
                'phone_verified_at' => now(),
                'is_active'        => true,
            ]
        );

        // Caretaker user
        $caretaker = User::firstOrCreate(
            ['phone' => '+254712000002'],
            [
                'name'             => 'Demo Caretaker',
                'role'             => 'caretaker',
                'phone_verified_at' => now(),
                'is_active'        => true,
            ]
        );

        $amenitiesFull  = ['water' => true, 'electricity' => true, 'wifi' => true,  'parking' => true,  'security' => true,  'borehole' => false, 'cctv' => true,  'gym' => false, 'pool' => false];
        $amenitiesBasic = ['water' => true, 'electricity' => true, 'wifi' => false, 'parking' => false, 'security' => true,  'borehole' => false, 'cctv' => false, 'gym' => false, 'pool' => false];
        $amenitiesMid   = ['water' => true, 'electricity' => true, 'wifi' => true,  'parking' => false, 'security' => true,  'borehole' => false, 'cctv' => false, 'gym' => false, 'pool' => false];

        $listings = [
            // ─── RONGAI ────────────────────────────────────────────────────
            [
                'estate_slug' => 'rongai', 'user_id' => $landlord->id,
                'title' => 'Spacious Bedsitter — Rongai Town',
                'description' => 'Well-lit bedsitter a few minutes from Rongai stage. Water 24/7, prepaid electricity. Caretaker on-site.',
                'type' => 'bedsitter', 'price' => 8500, 'deposit' => 8500,
                'street' => 'Rongai Town Road', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'James Mwangi', 'caretaker_phone' => '+254700111001', 'caretaker_whatsapp' => '+254700111001',
                'latitude' => -1.3950, 'longitude' => 36.7510, 'status' => 'active', 'verified_at' => now(), 'views_count' => 342,
            ],
            [
                'estate_slug' => 'rongai', 'user_id' => $landlord->id,
                'title' => 'Modern 1BR — Walking Distance to Matatu Stage',
                'description' => 'A clean modern 1-bedroom apartment. All amenities included. Parking available on request.',
                'type' => '1br', 'price' => 14000, 'deposit' => 14000,
                'street' => 'Rimpa Close, Rongai', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'Mary Wanjiku', 'caretaker_phone' => '+254700111002', 'caretaker_whatsapp' => '+254700111002',
                'latitude' => -1.3965, 'longitude' => 36.7525, 'status' => 'active', 'verified_at' => now(), 'views_count' => 215,
            ],
            [
                'estate_slug' => 'rongai', 'user_id' => $caretaker->id,
                'title' => 'Affordable Single Room — Near Tumaini',
                'description' => 'Basic single room with shared facilities. Very affordable for those starting out.',
                'type' => 'single_room', 'price' => 5500, 'deposit' => 5500,
                'street' => 'Tumaini Estate, Rongai', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Peter Kamau', 'caretaker_phone' => '+254700111003', 'caretaker_whatsapp' => '+254700111003',
                'latitude' => -1.3940, 'longitude' => 36.7500, 'status' => 'active', 'verified_at' => now(), 'views_count' => 128,
            ],
            [
                'estate_slug' => 'rongai', 'user_id' => $landlord->id,
                'title' => '2BR Family House — Rongai Milimani',
                'description' => 'Spacious 2-bedroom house in a quiet neighbourhood. Borehole water, CCTV.',
                'type' => '2br', 'price' => 22000, 'deposit' => 22000,
                'street' => 'Milimani Estate, Rongai', 'amenities' => $amenitiesFull,
                'caretaker_name' => 'Grace Achieng', 'caretaker_phone' => '+254700111004', 'caretaker_whatsapp' => '+254700111004',
                'latitude' => -1.3980, 'longitude' => 36.7545, 'status' => 'active', 'verified_at' => now(), 'views_count' => 87, 'is_featured' => true,
            ],

            // ─── ROYSAMBU ──────────────────────────────────────────────────
            [
                'estate_slug' => 'roysambu', 'user_id' => $landlord->id,
                'title' => 'Studio Apartment — Roysambu',
                'description' => 'Modern studio with WiFi, electric shower, secure compound.',
                'type' => 'studio', 'price' => 13500, 'deposit' => 13500,
                'street' => 'TRM Drive, Roysambu', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'David Otieno', 'caretaker_phone' => '+254700222001', 'caretaker_whatsapp' => '+254700222001',
                'latitude' => -1.2200, 'longitude' => 36.8840, 'status' => 'active', 'verified_at' => now(), 'views_count' => 401,
            ],
            [
                'estate_slug' => 'roysambu', 'user_id' => $caretaker->id,
                'title' => 'Bedsitter — Roysambu Estate B',
                'description' => 'Clean bedsitter, running water 24 hours, security guard at the gate.',
                'type' => 'bedsitter', 'price' => 9500, 'deposit' => 9500,
                'street' => 'Estate B Road, Roysambu', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Faith Njeri', 'caretaker_phone' => '+254700222002', 'caretaker_whatsapp' => '+254700222002',
                'latitude' => -1.2215, 'longitude' => 36.8855, 'status' => 'active', 'verified_at' => now(), 'views_count' => 188,
            ],
            [
                'estate_slug' => 'roysambu', 'user_id' => $landlord->id,
                'title' => '1BR — Near Roysambu Stage',
                'description' => 'One bedroom, all self-contained. Electricity prepaid.',
                'type' => '1br', 'price' => 15000, 'deposit' => 15000,
                'street' => 'Roysambu Main Road', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'Samuel Kimani', 'caretaker_phone' => '+254700222003', 'caretaker_whatsapp' => '+254700222003',
                'latitude' => -1.2195, 'longitude' => 36.8830, 'status' => 'pending', 'verified_at' => null, 'views_count' => 0,
            ],

            // ─── KASARANI ──────────────────────────────────────────────────
            [
                'estate_slug' => 'kasarani', 'user_id' => $landlord->id,
                'title' => 'Modern 2BR — Kasarani near Stadium',
                'description' => 'Spacious 2-bedroom, parking included, fibre WiFi.',
                'type' => '2br', 'price' => 28000, 'deposit' => 28000,
                'street' => 'Kasarani Stadium Road', 'amenities' => $amenitiesFull,
                'caretaker_name' => 'John Mutunga', 'caretaker_phone' => '+254700333001', 'caretaker_whatsapp' => '+254700333001',
                'latitude' => -1.2260, 'longitude' => 36.8975, 'status' => 'active', 'verified_at' => now(), 'views_count' => 294, 'is_featured' => true,
            ],
            [
                'estate_slug' => 'kasarani', 'user_id' => $caretaker->id,
                'title' => 'Bedsitter — Kasarani Sunton',
                'description' => 'Cosy bedsitter, 10 minutes walk to stage.',
                'type' => 'bedsitter', 'price' => 10000, 'deposit' => 10000,
                'street' => 'Sunton Estate, Kasarani', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Esther Wangari', 'caretaker_phone' => '+254700333002', 'caretaker_whatsapp' => '+254700333002',
                'latitude' => -1.2270, 'longitude' => 36.8990, 'status' => 'active', 'verified_at' => now(), 'views_count' => 155,
            ],

            // ─── PIPELINE ──────────────────────────────────────────────────
            [
                'estate_slug' => 'pipeline', 'user_id' => $landlord->id,
                'title' => 'Single Room — Pipeline Embakasi',
                'description' => 'Budget single room with shared bathrooms. Very affordable.',
                'type' => 'single_room', 'price' => 4500, 'deposit' => 4500,
                'street' => 'Pipeline Road, Embakasi', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Alex Ochieng', 'caretaker_phone' => '+254700444001', 'caretaker_whatsapp' => '+254700444001',
                'latitude' => -1.3055, 'longitude' => 36.8855, 'status' => 'active', 'verified_at' => now(), 'views_count' => 312,
            ],
            [
                'estate_slug' => 'pipeline', 'user_id' => $caretaker->id,
                'title' => 'Bedsitter — Pipeline Area C',
                'description' => 'Self-contained bedsitter in Pipeline. Quiet compound.',
                'type' => 'bedsitter', 'price' => 8000, 'deposit' => 8000,
                'street' => 'Area C Pipeline', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Rose Anyango', 'caretaker_phone' => '+254700444002', 'caretaker_whatsapp' => '+254700444002',
                'latitude' => -1.3065, 'longitude' => 36.8865, 'status' => 'active', 'verified_at' => now(), 'views_count' => 198,
            ],

            // ─── JUJA ──────────────────────────────────────────────────────
            [
                'estate_slug' => 'juja', 'user_id' => $landlord->id,
                'title' => 'Student Bedsitter — Near JKUAT',
                'description' => 'Ideal for students near JKUAT. WiFi enabled, 24hr water.',
                'type' => 'bedsitter', 'price' => 7000, 'deposit' => 7000,
                'street' => 'JKUAT Road, Juja', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'Kevin Muthoni', 'caretaker_phone' => '+254700555001', 'caretaker_whatsapp' => '+254700555001',
                'latitude' => -1.1010, 'longitude' => 37.0135, 'status' => 'active', 'verified_at' => now(), 'views_count' => 523, 'is_featured' => true,
            ],
            [
                'estate_slug' => 'juja', 'user_id' => $caretaker->id,
                'title' => '1BR — Juja Farm Road',
                'description' => 'Clean 1BR, good for couples or small families.',
                'type' => '1br', 'price' => 12000, 'deposit' => 12000,
                'street' => 'Juja Farm Road', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Mercy Chebet', 'caretaker_phone' => '+254700555002', 'caretaker_whatsapp' => '+254700555002',
                'latitude' => -1.1025, 'longitude' => 37.0150, 'status' => 'active', 'verified_at' => now(), 'views_count' => 267,
            ],

            // ─── KITENGELA ─────────────────────────────────────────────────
            [
                'estate_slug' => 'kitengela', 'user_id' => $landlord->id,
                'title' => '3BR Executive — Kitengela Township',
                'description' => 'Spacious 3 bedroom house, ideal for families. Gated compound, ample parking.',
                'type' => '3br', 'price' => 38000, 'deposit' => 38000,
                'street' => 'Kitengela Township Road', 'amenities' => $amenitiesFull,
                'caretaker_name' => 'Daniel Kipkorir', 'caretaker_phone' => '+254700666001', 'caretaker_whatsapp' => '+254700666001',
                'latitude' => -1.4728, 'longitude' => 36.9645, 'status' => 'active', 'verified_at' => now(), 'views_count' => 176,
            ],
            [
                'estate_slug' => 'kitengela', 'user_id' => $caretaker->id,
                'title' => 'Bedsitter — Kitengela Acacia Estate',
                'description' => 'Modern bedsitter. Water tank on-site, CCTV compound.',
                'type' => 'bedsitter', 'price' => 9000, 'deposit' => 9000,
                'street' => 'Acacia Estate, Kitengela', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'Lucy Wanyama', 'caretaker_phone' => '+254700666002', 'caretaker_whatsapp' => '+254700666002',
                'latitude' => -1.4742, 'longitude' => 36.9660, 'status' => 'active', 'verified_at' => now(), 'views_count' => 93,
            ],

            // ─── KAHAWA ────────────────────────────────────────────────────
            [
                'estate_slug' => 'kahawa', 'user_id' => $landlord->id,
                'title' => '1BR — Kahawa West',
                'description' => 'Neat 1 bedroom, electric shower, WiFi ready.',
                'type' => '1br', 'price' => 13000, 'deposit' => 13000,
                'street' => 'Kahawa West', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'Isaac Mutua', 'caretaker_phone' => '+254700777001', 'caretaker_whatsapp' => '+254700777001',
                'latitude' => -1.1820, 'longitude' => 36.9210, 'status' => 'active', 'verified_at' => now(), 'views_count' => 203,
            ],

            // ─── NGONG ─────────────────────────────────────────────────────
            [
                'estate_slug' => 'ngong', 'user_id' => $landlord->id,
                'title' => '2BR — Ngong Hills View',
                'description' => 'Breathtaking views of Ngong Hills. Spacious 2BR with parking.',
                'type' => '2br', 'price' => 20000, 'deposit' => 20000,
                'street' => 'Hills View Road, Ngong', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'Ann Wangui', 'caretaker_phone' => '+254700888001', 'caretaker_whatsapp' => '+254700888001',
                'latitude' => -1.3588, 'longitude' => 36.6582, 'status' => 'active', 'verified_at' => now(), 'views_count' => 145,
            ],

            // ─── GITHURAI ──────────────────────────────────────────────────
            [
                'estate_slug' => 'githurai', 'user_id' => $landlord->id,
                'title' => 'Bedsitter — Githurai 45',
                'description' => 'Affordable bedsitter in Githurai 45. Close to the stage.',
                'type' => 'bedsitter', 'price' => 8500, 'deposit' => 8500,
                'street' => 'Githurai 45', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Vincent Omondi', 'caretaker_phone' => '+254700999001', 'caretaker_whatsapp' => '+254700999001',
                'latitude' => -1.1755, 'longitude' => 36.9125, 'status' => 'active', 'verified_at' => now(), 'views_count' => 231,
            ],
            [
                'estate_slug' => 'githurai', 'user_id' => $caretaker->id,
                'title' => 'Single Room — Githurai 44',
                'description' => 'Budget-friendly single room for working professionals.',
                'type' => 'single_room', 'price' => 5000, 'deposit' => 5000,
                'street' => 'Githurai 44 Estate', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Helen Auma', 'caretaker_phone' => '+254700999002', 'caretaker_whatsapp' => '+254700999002',
                'latitude' => -1.1768, 'longitude' => 36.9138, 'status' => 'active', 'verified_at' => now(), 'views_count' => 167,
            ],

            // ─── RUAKA ─────────────────────────────────────────────────────
            [
                'estate_slug' => 'ruaka', 'user_id' => $landlord->id,
                'title' => 'Modern Studio — Ruaka Town',
                'description' => 'Brand new studio apartment. Fitted kitchen, fibre WiFi.',
                'type' => 'studio', 'price' => 16000, 'deposit' => 16000,
                'street' => 'Ruaka Town Centre', 'amenities' => $amenitiesFull,
                'caretaker_name' => 'Brian Ngugi', 'caretaker_phone' => '+254701000001', 'caretaker_whatsapp' => '+254701000001',
                'latitude' => -1.2055, 'longitude' => 36.8010, 'status' => 'active', 'verified_at' => now(), 'views_count' => 389, 'is_featured' => true,
            ],

            // ─── KIJABE VILLAGES ───────────────────────────────────────────
            [
                'estate_slug' => 'maingi', 'user_id' => $landlord->id,
                'title' => '2BR Bungalow — Maingi Area, Kijabe',
                'description' => 'Beautiful bungalow surrounded by mature trees. Serene environment, secure compound.',
                'type' => '2br', 'price' => 18000, 'deposit' => 18000,
                'street' => 'Maingi Village Road', 'amenities' => $amenitiesMid,
                'caretaker_name' => 'Joseph Njoroge', 'caretaker_phone' => '+254700000101', 'caretaker_whatsapp' => '+254700000101',
                'latitude' => -0.9388, 'longitude' => 36.5932, 'status' => 'active', 'verified_at' => now(), 'views_count' => 54,
            ],
            [
                'estate_slug' => 'kimende', 'user_id' => $caretaker->id,
                'title' => 'Spacious 1BR — Kimende Center',
                'description' => 'Neat 1BR close to Nakuru-Nairobi highway. Good water supply and instant hot shower.',
                'type' => '1br', 'price' => 11000, 'deposit' => 11000,
                'street' => 'Highway View Road', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Samuel Karanja', 'caretaker_phone' => '+254700000102', 'caretaker_whatsapp' => '+254700000102',
                'latitude' => -0.9634, 'longitude' => 36.5828, 'status' => 'active', 'verified_at' => now(), 'views_count' => 95, 'is_featured' => true,
            ],
            [
                'estate_slug' => 'kingatua', 'user_id' => $landlord->id,
                'title' => 'Cozy Bedsitter — Kingatua Village',
                'description' => 'Affordable bedsitter with self-contained bathroom. Safe and quiet neighbourhood.',
                'type' => 'bedsitter', 'price' => 6000, 'deposit' => 6000,
                'street' => 'Kingatua Primary Road', 'amenities' => $amenitiesBasic,
                'caretaker_name' => 'Hannah Wambui', 'caretaker_phone' => '+254700000103', 'caretaker_whatsapp' => '+254700000103',
                'latitude' => -0.9421, 'longitude' => 36.5744, 'status' => 'active', 'verified_at' => now(), 'views_count' => 41,
            ],
        ];

        $created = 0;
        foreach ($listings as $data) {
            $estateSlug = $data['estate_slug'];
            $estate = $estates->get($estateSlug);
            if (! $estate) continue;

            unset($data['estate_slug']);
            $data['estate_id'] = $estate->id;
            $data['is_featured'] = $data['is_featured'] ?? false;

            Listing::firstOrCreate(
                ['title' => $data['title'], 'estate_id' => $estate->id],
                $data
            );
            $created++;
        }

        // Update listing_count on each estate
        foreach ($estates as $estate) {
            $estate->update(['listing_count' => $estate->activeListings()->count()]);
        }

        $this->command->info("Seeded {$created} listings.");
    }
}
