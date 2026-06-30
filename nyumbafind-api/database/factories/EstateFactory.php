<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EstateFactory extends Factory
{
    protected $model = \App\Models\Estate::class;

    public function definition(): array
    {
        $name = $this->faker->city;
        return [
            'name'          => $name,
            'slug'          => Str::slug($name),
            'county'        => $this->faker->randomElement(['Nairobi', 'Kiambu', 'Kajiado', 'Machakos']),
            'sub_county'    => $this->faker->word,
            'latitude'      => $this->faker->latitude(-1.5, -1.0),
            'longitude'     => $this->faker->longitude(36.6, 37.1),
            'is_active'     => true,
            'listing_count' => 0,
        ];
    }
}
