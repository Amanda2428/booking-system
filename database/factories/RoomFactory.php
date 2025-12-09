<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => \App\Models\RoomType::factory(),
            'name' => $this->faker->unique()->words(2, true), 
            'capacity' => $this->faker->numberBetween(2, 20),
            'location' => $this->faker->city(),
            'description' => $this->faker->sentence(),
            'availability_status' => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
}
