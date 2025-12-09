<?php

namespace Database\Factories;
use App\Models\Feedback;
use App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::factory()->create();
        return [
            'booking_id' => $booking->id,
            'user_id'    => $booking->user_id,
            'room_id'    => $booking->room_id,

            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(10),
            'admin_reply' => fake()->optional()->sentence(8),
        ];
    }
}
