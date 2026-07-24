<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Committee;
use App\Models\Initiative;
use Database\Seeders\ProjectStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_root_redirects_to_education_committee(): void
    {
        $this->seed(ProjectStructureSeeder::class);

        $response = $this->get('/projects');

        $response->assertRedirect('/projects/sk-namayan-youth-services/committees/education');
    }

    public function test_explorer_fails_on_mismatch(): void
    {
        $this->seed(ProjectStructureSeeder::class);

        // Initiative ID 1 is Silid (education), but we try to access it via health slug
        $response = $this->get('/projects/sk-namayan-youth-services/committees/health/initiatives/1');

        $response->assertNotFound();
    }

    public function test_committee_page_displays_assigned_officials(): void
    {
        $this->seed(ProjectStructureSeeder::class);

        $committee = Committee::where('slug', 'education')->firstOrFail();
        $official = \App\Models\SkOfficial::create([
            'name' => 'JUAN DELA CRUZ',
            'position' => 'Committee Member',
            'committee_id' => $committee->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/projects/sk-namayan-youth-services/committees/education');
        $response->assertOk();
        $response->assertSee('JUAN DELA CRUZ');
        $response->assertSee('Committee Member');
    }
}
