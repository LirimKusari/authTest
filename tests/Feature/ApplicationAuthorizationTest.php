<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccess();
    }

    public function test_applicant_can_apply_and_cannot_write_internal_fields(): void
    {
        $job = Job::factory()->create(['created_by' => $this->manager()->id]);
        $applicant = $this->applicant();

        $this->actingAs($applicant)
            ->postJson("/api/jobs/{$job->id}/applications", [
                'cover_letter' => 'I am interested.',
                'internal_notes' => 'Please hire me quietly.',
                'score' => 10,
            ])
            ->assertForbidden()
            ->assertJsonPath('fields', ['internal_notes', 'score']);

        $created = $this->actingAs($applicant)->postJson("/api/jobs/{$job->id}/applications", [
            'cover_letter' => 'I am interested.',
            'resume_url' => 'https://example.com/resume.pdf',
        ]);

        $created
            ->assertCreated()
            ->assertJsonPath('data.cover_letter', 'I am interested.')
            ->assertJsonPath('abilities.sections', ['overview']);

        $this->assertArrayNotHasKey('internal_notes', $created->json('data'));
        $this->assertArrayNotHasKey('score', $created->json('data'));
    }

    public function test_applicant_cannot_see_other_applications(): void
    {
        $job = Job::factory()->create(['created_by' => $this->manager()->id]);
        $owner = $this->applicant();
        $other = $this->applicant();
        $application = Application::factory()->create([
            'job_id' => $job->id,
            'user_id' => $owner->id,
            'internal_notes' => 'Keep private',
            'score' => 9,
        ]);

        $this->actingAs($other)
            ->getJson("/api/applications/{$application->id}")
            ->assertNotFound();

        $this->actingAs($other)
            ->getJson('/api/applications')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_manager_sees_internal_review_and_can_score(): void
    {
        $manager = $this->manager();
        $job = Job::factory()->create(['created_by' => $manager->id]);
        $application = Application::factory()->create([
            'job_id' => $job->id,
            'user_id' => $this->applicant()->id,
            'internal_notes' => 'Needs a follow-up',
            'score' => 6,
        ]);

        $this->actingAs($manager)
            ->getJson("/api/applications/{$application->id}")
            ->assertOk()
            ->assertJsonPath('data.internal_notes', 'Needs a follow-up')
            ->assertJsonPath('data.score', 6)
            ->assertJsonPath('abilities.sections', ['overview', 'internal_review']);

        $this->actingAs($manager)
            ->putJson("/api/applications/{$application->id}", [
                'status' => 'reviewing',
                'score' => 8,
                'internal_notes' => 'Advance to onsite',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'reviewing')
            ->assertJsonPath('data.score', 8);
    }

    public function test_applicant_cannot_change_status(): void
    {
        $job = Job::factory()->create(['created_by' => $this->manager()->id]);
        $applicant = $this->applicant();
        $application = Application::factory()->create([
            'job_id' => $job->id,
            'user_id' => $applicant->id,
        ]);

        $this->actingAs($applicant)
            ->putJson("/api/applications/{$application->id}", [
                'status' => 'hired',
            ])
            ->assertForbidden()
            ->assertJsonPath('fields', ['status']);
    }

    public function test_manager_cannot_delete_applications(): void
    {
        $application = Application::factory()->create([
            'job_id' => Job::factory()->create(['created_by' => $this->manager()->id])->id,
            'user_id' => $this->applicant()->id,
        ]);

        $this->actingAs($this->manager())
            ->deleteJson("/api/applications/{$application->id}")
            ->assertForbidden();
    }

    public function test_admin_can_delete_applications(): void
    {
        $application = Application::factory()->create([
            'job_id' => Job::factory()->create(['created_by' => $this->manager()->id])->id,
            'user_id' => $this->applicant()->id,
        ]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/applications/{$application->id}")
            ->assertNoContent();
    }
}
