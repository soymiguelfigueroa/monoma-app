<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'source' => fake()->word(),
            'owner' => User::factory()->create(['role' => 'agent'])->id,
            'created_at' => fake()->dateTime('-1 week'),
            'created_by' => User::factory()->create(['role' => 'manager'])->id,
        ];
    }
}
