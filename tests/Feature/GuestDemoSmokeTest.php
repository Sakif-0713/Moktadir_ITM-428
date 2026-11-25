<?php

namespace Tests\Feature;

use App\Models\Workspace;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestDemoSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_demo_login_and_primary_routes_return_ok(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/demo/guest-login');
        $workspace = Workspace::firstOrFail();

        $response->assertRedirect(route('workspaces.show', $workspace));

        $this->get(route('workspaces.index'))->assertOk();
        $this->get(route('workspaces.show', $workspace))->assertOk();
        $this->get(route('evaluations.index', $workspace))->assertOk();
        $this->get(route('refactor-logs.index', $workspace))->assertOk();

        $doc = $workspace->documents()->firstOrFail();
        $this->get(route('documents.show', [$workspace, $doc]))->assertOk();

        $prompt = $workspace->promptTemplates()->firstOrFail();
        $this->get(route('prompts.show', [$workspace, $prompt]))->assertOk();

        $run = $workspace->testRuns()->firstOrFail();
        $this->get(route('test-runs.show', [$workspace, $run]))->assertOk();
    }

    public function test_empty_document_submission_fails_validation(): void
    {
        $this->seed(DatabaseSeeder::class);
        $workspace = Workspace::firstOrFail();

        $response = $this->post(route('documents.store', $workspace), [
            'title' => 'Empty Doc',
            'raw_content' => '   ',
            'chunking_strategy' => 'fixed_size',
        ]);

        $response->assertSessionHasErrors('raw_content');
    }

    public function test_invalid_workspace_id_returns_404(): void
    {
        $this->get('/workspaces/999999')->assertNotFound();
    }
}
