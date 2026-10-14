<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(2, true),
            'location' => fake()->city(),
            'employment_type' => 'full_time',
            'status' => Job::STATUS_PUBLISHED,
            'salary_min' => 60000,
            'salary_max' => 90000,
            'hiring_budget' => 120000,
            'created_by' => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => Job::STATUS_DRAFT,
        ]);
    }
}
