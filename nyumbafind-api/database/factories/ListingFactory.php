<?php

namespace Database\Factories;

use App\Models\Estate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    protected $model = \App\Models\Listing::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['bedsitter', '1br', '2br', '3br', 'single_room', 'studio']);
        $priceMap = [
            'single_room' => [4000,  8000],
            'bedsitter'   => [7000,  15000],
            'studio'      => [10000, 18000],
            '1br'         => [12000, 25000],
            '2br'         => [20000, 40000],
            '3br'         => [35000, 70000],
        ];
        [$min, $max] = $priceMap[$type];

        $amenityList = ['water', 'electricity', 'wifi', 'parking', 'security', 'borehole', 'cctv', 'gym', 'pool'];
        $selectedAmenities = $this->faker->randomElements($amenityList, $this->faker->numberBetween(2, 6));
        $amenities = collect($amenityList)->mapWithKeys(fn ($a) => [$a => in_array($a, $selectedAmenities)])->toArray();

        $caretakerPhone = '+2547' . $this->faker->numerify('########');

        $titles = [
            "Spacious {$type} in great location",
            "Modern {$type} — walking distance to stage",
            "Affordable {$type} — all amenities included",
            "Clean {$type} — caretaker on-site",
            "Brand new {$type} — first tenant",
        ];

        return [
            'user_id'              => User::factory(),
            'estate_id'            => Estate::factory(),
            'title'                => $this->faker->randomElement($titles),
            'description'          => $this->faker->sentences(3, true),
            'type'                 => $type,
            'price'                => $this->faker->numberBetween($min, $max),
            'deposit'              => $this->faker->numberBetween($min, $max),
            'street'               => $this->faker->streetAddress,
            'amenities'            => $amenities,
            'caretaker_name'       => $this->faker->name,
            'caretaker_phone'      => $caretakerPhone,
            'caretaker_whatsapp'   => $caretakerPhone,
            'latitude'             => $this->faker->latitude(-1.5, -1.0),
            'longitude'            => $this->faker->longitude(36.6, 37.1),
            'status'               => $this->faker->randomElement(['active', 'active', 'active', 'pending', 'occupied']),
            'views_count'          => $this->faker->numberBetween(0, 500),
            'vacancy_confirmed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active', 'verified_at' => now(), 'vacancy_confirmed_at' => now()]);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending', 'verified_at' => null]);
    }
}
