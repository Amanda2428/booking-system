<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Room;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $startTime = $this->faker->time('H:i');
        $endTime = date('H:i', strtotime($startTime . ' +1 hour'));
        return [
            'user_id' => User::factory(),  
            'room_id' => Room::factory(),   

            'date' => $this->faker->date(),
            'start_time' => $startTime,
            'end_time' => $endTime,

            'status' => $this->faker->randomElement(['pending', 'approved', 'cancelled']),
        ];
    }
}
