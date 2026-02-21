<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Create Admin User
        User::create([
            'name' => 'Vudo Admin',
            'email' => 'vudo@houseforrent.com',
            'password' => bcrypt('vudo123'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '+260970000000',
            'country' => 'Zambia',
            'email_verified_at' => now(),
        ]);

        $this->call([
            DealerSeeder::class,
            SettingsSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
