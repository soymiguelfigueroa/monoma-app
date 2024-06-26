<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();
        
        $manager = \App\Models\User::factory()->create([
            'username' => 'tester',
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'manager'
        ]);

        $agent = \App\Models\User::factory()->create([
            'username' => 'agent_tester',
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'agent'
        ]);

        \App\Models\Candidate::factory(10)->create([
            'owner' => $agent->id,
            'created_by' => $manager->id
        ]);
    }
}
