<?php

namespace Database\Seeders;

use App\Authorization\Catalog\Roles;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole(Roles::ADMIN);

        $manager = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        $manager->assignRole(Roles::MANAGER);

        $applicant = User::factory()->create([
            'name' => 'Applicant',
            'email' => 'applicant@example.com',
        ]);
        $applicant->assignRole(Roles::APPLICANT);

        $published = Job::factory()->create([
            'title' => 'Backend Engineer',
            'status' => Job::STATUS_PUBLISHED,
            'created_by' => $manager->id,
            'salary_min' => 80000,
            'salary_max' => 110000,
            'hiring_budget' => 150000,
        ]);

        Job::factory()->draft()->create([
            'title' => 'Draft Platform Role',
            'created_by' => $manager->id,
            'salary_min' => 90000,
            'salary_max' => 130000,
            'hiring_budget' => 180000,
        ]);

        Application::factory()->create([
            'job_id' => $published->id,
            'user_id' => $applicant->id,
            'cover_letter' => 'I would like to join the backend team.',
            'internal_notes' => 'Strong Laravel background.',
            'score' => 8,
        ]);
    }
}
