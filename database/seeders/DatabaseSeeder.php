<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {  $this->call([
        StatusSeeder::class,
        PrioritySeeder::class
    ]);
        // Create a single admin user
        User::factory()->admin()->create();

        // Optionally, create additional random users
        User::factory(10)->create(); // Creates 10 random users

    }
}
