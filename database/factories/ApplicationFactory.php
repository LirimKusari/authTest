<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'user_id' => User::factory(),
            'cover_letter' => fake()->paragraph(),
            'resume_url' => fake()->url(),
            'status' => Application::STATUS_SUBMITTED,
            'internal_notes' => 'Promising candidate.',
            'score' => 7,
        ];
    }
}
