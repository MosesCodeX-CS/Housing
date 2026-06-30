<?php

namespace Database\Seeders;

use App\Models\Estate;
use Illuminate\Database\Seeder;

class EstateSeeder extends Seeder
{
    public function run(): void
    {
        $estates = [
            ['name' => 'Rongai',    'slug' => 'rongai',    'county' => 'Kajiado', 'sub_county' => 'Ongata Rongai', 'ward' => 'Rongai', 'latitude' => -1.3961, 'longitude' => 36.7516],
            ['name' => 'Roysambu',  'slug' => 'roysambu',  'county' => 'Nairobi', 'sub_county' => 'Roysambu',      'ward' => 'Roysambu', 'latitude' => -1.2209, 'longitude' => 36.8849],
            ['name' => 'Juja',      'slug' => 'juja',      'county' => 'Kiambu',  'sub_county' => 'Juja',          'ward' => 'Juja', 'latitude' => -1.1017, 'longitude' => 37.0144],
            ['name' => 'Pipeline',  'slug' => 'pipeline',  'county' => 'Nairobi', 'sub_county' => 'Embakasi',      'ward' => 'Pipeline', 'latitude' => -1.3060, 'longitude' => 36.8860],
            ['name' => 'Kasarani',  'slug' => 'kasarani',  'county' => 'Nairobi', 'sub_county' => 'Kasarani',      'ward' => 'Kasarani', 'latitude' => -1.2268, 'longitude' => 36.8984],
            ['name' => 'Ngong',     'slug' => 'ngong',     'county' => 'Kajiado', 'sub_county' => 'Ngong',         'ward' => 'Ngong', 'latitude' => -1.3593, 'longitude' => 36.6589],
            ['name' => 'Kahawa',    'slug' => 'kahawa',    'county' => 'Nairobi', 'sub_county' => 'Kasarani',      'ward' => 'Kahawa West', 'latitude' => -1.1814, 'longitude' => 36.9213],
            ['name' => 'Ruaka',     'slug' => 'ruaka',     'county' => 'Kiambu',  'sub_county' => 'Kiambaa',       'ward' => 'Ruaka', 'latitude' => -1.2059, 'longitude' => 36.8012],
            ['name' => 'Githurai',  'slug' => 'githurai',  'county' => 'Nairobi', 'sub_county' => 'Roysambu',      'ward' => 'Githurai 44', 'latitude' => -1.1761, 'longitude' => 36.9130],
            ['name' => 'Kitengela', 'slug' => 'kitengela', 'county' => 'Kajiado', 'sub_county' => 'Athi River',    'ward' => 'Kitengela', 'latitude' => -1.4735, 'longitude' => 36.9654],
            
            // Kijabe Villages in Lari sub-county
            ['name' => 'Maingi',    'slug' => 'maingi',    'county' => 'Kiambu',  'sub_county' => 'Lari',          'ward' => 'Kijabe', 'latitude' => -0.9388, 'longitude' => 36.5932],
            ['name' => 'Kimende',   'slug' => 'kimende',   'county' => 'Kiambu',  'sub_county' => 'Lari',          'ward' => 'Kijabe', 'latitude' => -0.9634, 'longitude' => 36.5828],
            ['name' => 'Kingatua',  'slug' => 'kingatua',  'county' => 'Kiambu',  'sub_county' => 'Lari',          'ward' => 'Kijabe', 'latitude' => -0.9421, 'longitude' => 36.5744],
        ];

        foreach ($estates as $estate) {
            Estate::firstOrCreate(
                ['slug' => $estate['slug']],
                array_merge($estate, ['is_active' => true, 'listing_count' => 0])
            );
        }

        $this->command->info('Seeded ' . count($estates) . ' estates.');
    }
}
