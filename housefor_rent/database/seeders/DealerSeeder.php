<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DealerSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 Gold Dealers
        User::factory(20)->create([
            'role' => 'dealer',
            'subscription_plan' => 'gold',
            'subscription_expires_at' => now()->addMonth(),
            'status' => 'active',
        ]);
        
        // Create 10 Basic Dealers
        User::factory(10)->create([
            'role' => 'dealer',
            'subscription_plan' => 'basic',
            'subscription_expires_at' => null,
            'status' => 'active',
        ]);
    }
}
