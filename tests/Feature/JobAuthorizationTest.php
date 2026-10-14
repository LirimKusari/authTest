<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccess();
    }

    public function test_applicant_sees_only_published_jobs_without_financial_fields(): void
    {
        $manager = $this->manager();
        $published = Job::factory()->create([
            'title' => 'Published Role',
            'created_by' => $manager->id,
            'hiring_budget' => 150000,
        ]);
        $draft = Job::factory()->draft()->create([
            'title' => 'Draft Role',
            'created_by' => $manager->id,
            'hiring_budget' => 200000,
        ]);

        $response = $this->actingAs($this->applicant())->getJson('/api/jobs');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($published->id));
        $this->assertFalse($ids->contains($draft->id));
        $this->assertArrayNotHasKey('hiring_budget', $response->json('data.0'));
        $this->assertSame(['title', 'description', 'location', 'employment_type', 'status'], $response->json('data.0.abilities.readable_fields'));
    }

    public function test_manager_sees_draft_jobs_and_financial_fields(): void
    {
        $manager = $this->manager();
        $draft = Job::factory()->draft()->create([
            'created_by' => $manager->id,
            'hiring_budget' => 200000,
        ]);

        $response = $this->actingAs($manager)->getJson("/api/jobs/{$draft->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.hiring_budget', 200000)
            ->assertJsonPath('abilities.sections', ['overview', 'financials'])
            ->assertJsonPath('abilities.actions.update', true)
            ->assertJsonPath('abilities.actions.delete', false);
    }

    public function test_applicant_cannot_open_a_draft_job(): void
    {
        $draft = Job::factory()->draft()->create([
            'created_by' => $this->manager()->id,
        ]);

        $this->actingAs($this->applicant())
            ->getJson("/api/jobs/{$draft->id}")
            ->assertNotFound();
    }

    public function test_applicant_cannot_create_jobs(): void
    {
        $this->actingAs($this->applicant())
            ->postJson('/api/jobs', [
                'title' => 'Nope',
                'description' => 'Nope',
                'location' => 'Remote',
                'employment_type' => 'full_time',
            ])
            ->assertForbidden();
    }

    public function test_manager_can_create_and_update_financial_fields(): void
    {
        $manager = $this->manager();

        $created = $this->actingAs($manager)->postJson('/api/jobs', [
            'title' => 'Staff Engineer',
            'description' => 'Build APIs',
            'location' => 'Remote',
            'employment_type' => 'full_time',
            'status' => 'published',
            'hiring_budget' => 175000,
        ]);

        $created
            ->assertCreated()
            ->assertJsonPath('data.hiring_budget', 175000)
            ->assertJsonPath('data.title', 'Staff Engineer');

        $id = $created->json('data.id');

        $this->actingAs($manager)
            ->putJson("/api/jobs/{$id}", ['hiring_budget' => 190000])
            ->assertOk()
            ->assertJsonPath('data.hiring_budget', 190000);
    }

    public function test_manager_cannot_delete_jobs(): void
    {
        $manager = $this->manager();
        $job = Job::factory()->create(['created_by' => $manager->id]);

        $this->actingAs($manager)
            ->deleteJson("/api/jobs/{$job->id}")
            ->assertForbidden();
    }

    public function test_admin_can_delete_jobs(): void
    {
        $job = Job::factory()->create(['created_by' => $this->manager()->id]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/jobs/{$job->id}")
            ->assertNoContent();
    }
}
