<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Initiative;
use App\Models\AccomplishmentReport;
use Database\Seeders\ProjectStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccomplishmentReportAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_reports_manager(): void
    {
        $response = $this->get('/admin/reports');
        $response->assertRedirect('/login');
    }

    public function test_non_admins_cannot_access_reports_manager(): void
    {
        $this->seed(ProjectStructureSeeder::class);
        $roles = ['staff', 'user'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get('/admin/reports');
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_upload_accomplishment_report(): void
    {
        Storage::fake('public');
        $this->seed(ProjectStructureSeeder::class);
        $user = User::factory()->create(['role' => 'admin']);
        $initiative = Initiative::first();

        $file = UploadedFile::fake()->create('test_report.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)->post('/admin/reports', [
            'report_title' => 'Q2 Special Board Report',
            'initiative_id' => $initiative->id,
            'file' => $file,
            'reporting_period' => '2026-06-30',
        ]);

        $response->assertRedirect('/admin/reports');
        $this->assertDatabaseHas('accomplishment_reports', [
            'report_title' => 'Q2 Special Board Report',
            'initiative_id' => $initiative->id,
        ]);

        // Verify storage upload
        $report = AccomplishmentReport::where('report_title', 'Q2 Special Board Report')->first();
        Storage::disk('public')->assertExists($report->file_path);
    }

    public function test_admin_can_edit_accomplishment_report(): void
    {
        Storage::fake('public');
        $this->seed(ProjectStructureSeeder::class);
        $user = User::factory()->create(['role' => 'admin']);
        $initiative = Initiative::first();
        $report = AccomplishmentReport::create([
            'report_title' => 'Initial Report',
            'initiative_id' => $initiative->id,
            'file_path' => 'reports/test.pdf',
            'reporting_period' => '2026-06-30',
        ]);

        $response = $this->actingAs($user)->put("/admin/reports/{$report->id}", [
            'report_title' => 'Updated Report Title',
            'initiative_id' => $report->initiative_id,
            'reporting_period' => '2026-07-01',
        ]);

        $response->assertRedirect('/admin/reports');
        $this->assertDatabaseHas('accomplishment_reports', [
            'id' => $report->id,
            'report_title' => 'Updated Report Title',
        ]);
    }

    public function test_admin_can_delete_accomplishment_report(): void
    {
        Storage::fake('public');
        $this->seed(ProjectStructureSeeder::class);
        $user = User::factory()->create(['role' => 'admin']);
        $initiative = Initiative::first();
        $report = AccomplishmentReport::create([
            'report_title' => 'To Delete Report',
            'initiative_id' => $initiative->id,
            'file_path' => 'reports/delete.pdf',
            'reporting_period' => '2026-06-30',
        ]);

        $response = $this->actingAs($user)->delete("/admin/reports/{$report->id}");

        $response->assertRedirect('/admin/reports');
        $this->assertDatabaseMissing('accomplishment_reports', [
            'id' => $report->id,
        ]);
    }
}
