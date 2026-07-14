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

    public function test_committee_explorer_renders_correct_details(): void
    {
        $this->seed(ProjectStructureSeeder::class);

        $response = $this->get('/projects/sk-namayan-youth-services/committees/education');

        $response->assertOk();
        $response->assertSee('Education &amp; Library Services', false);
        $response->assertSee('Silid Karunungan Booking');
        $response->assertSee('TIPD Scholarship Program');
        $response->assertSee('Alternative Learning System');
        // Displays accomplishments at the top
        $response->assertSee('Q1 Silid Karunungan Booking Attendance Report');
    }

    public function test_initiative_explorer_renders_active_initiative(): void
    {
        $this->seed(ProjectStructureSeeder::class);

        $response = $this->get('/projects/sk-namayan-youth-services/committees/education/initiatives/1');

        $response->assertOk();
        $response->assertSee('Silid Karunungan Booking');
        $response->assertSee('Q1 Silid Karunungan Booking Attendance Report');
    }

    public function test_explorer_fails_on_mismatch(): void
    {
        $this->seed(ProjectStructureSeeder::class);

        // Initiative ID 1 is Silid (education), but we try to access it via health slug
        $response = $this->get('/projects/sk-namayan-youth-services/committees/health/initiatives/1');

        $response->assertNotFound();
    }
}
