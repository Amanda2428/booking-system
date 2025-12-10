<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::factory()->create([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'role' => 1,
            'password' => bcrypt(env('ADMIN_PASSWORD')),
        ]);

        // Students
    \App\Models\User::factory(10)->create();

    // Room categories
    \App\Models\RoomType::factory(5)->create();

    // Rooms
    \App\Models\Room::factory(20)->create();

    // Bookings
    \App\Models\Booking::factory(30)->create();

    // Feedback
    \App\Models\Feedback::factory(20)->create();
    }
}
