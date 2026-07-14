<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Committee;
use App\Models\Initiative;
use App\Models\AccomplishmentReport;
use Database\Seeders\ProjectStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructureManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_structure_manager(): void
    {
        $response = $this->get('/admin/structure');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_mutate_structure(): void
    {
        $response = $this->post('/admin/structure/committees', ['name' => 'New Committee']);
        $response->assertRedirect('/login');

        $response2 = $this->delete('/admin/structure/committees/1');
        $response2->assertRedirect('/login');
    }

    public function test_standard_user_cannot_access_structure_manager(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/structure');
        $response->assertStatus(403);
    }

    public function test_standard_user_cannot_mutate_structure(): void
    {
        $this->seed(ProjectStructureSeeder::class);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/admin/structure/committees', ['name' => 'New Committee']);
        $response->assertStatus(403);

        $committee = Committee::first();
        $response2 = $this->actingAs($user)->delete("/admin/structure/committees/{$committee->id}");
        $response2->assertStatus(403);
    }

    public function test_non_superadmins_cannot_access_structure_manager(): void
    {
        $this->seed(ProjectStructureSeeder::class);
        $roles = ['staff', 'admin'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);

            // Cannot view list
            $response = $this->actingAs($user)->get('/admin/structure');
            $response->assertStatus(403);

            // Cannot mutate
            $responsePostCommittee = $this->actingAs($user)->post('/admin/structure/committees', ['name' => 'New Committee']);
            $responsePostCommittee->assertStatus(403);

            $committee = Committee::first();
            $responseDeleteCommittee = $this->actingAs($user)->delete("/admin/structure/committees/{$committee->id}");
            $responseDeleteCommittee->assertStatus(403);

            $responsePostInitiative = $this->actingAs($user)->post('/admin/structure/initiatives', [
                'committee_id' => $committee->id,
                'title' => 'New Initiative',
                'description' => 'Test Desc',
            ]);
            $responsePostInitiative->assertStatus(403);

            $initiative = Initiative::first();
            $responseDeleteInitiative = $this->actingAs($user)->delete("/admin/structure/initiatives/{$initiative->id}");
            $responseDeleteInitiative->assertStatus(403);
        }
    }

    public function test_superadmin_can_manage_structure(): void
    {
        $this->seed(ProjectStructureSeeder::class);
        $user = User::factory()->create(['role' => 'superadmin']);

        // Superadmin can view and see forms
        $response = $this->actingAs($user)->get('/admin/structure');
        $response->assertOk();
        $response->assertSee('Add New Committee');
        $response->assertSee('Add New Initiative');

        // Superadmin can store a committee
        $responseStoreCommittee = $this->actingAs($user)->post('/admin/structure/committees', [
            'name' => 'New Administrative Committee',
        ]);
        $responseStoreCommittee->assertRedirect();
        $this->assertDatabaseHas('committees', [
            'name' => 'New Administrative Committee',
            'slug' => 'new-administrative-committee',
        ]);

        $newCommittee = Committee::where('slug', 'new-administrative-committee')->first();

        // Superadmin can store an initiative with custom fields
        $responseStoreInitiative = $this->actingAs($user)->post('/admin/structure/initiatives', [
            'committee_id' => $newCommittee->id,
            'title' => 'New Admin Initiative',
            'description' => 'A program details description.',
            'form_route' => 'forms.health.create',
            'custom_fields' => [
                [
                    'label' => 'School Name',
                    'name' => 'school_name',
                    'type' => 'text',
                    'placeholder' => 'Enter school...',
                    'required' => '1',
                ]
            ],
        ]);
        $responseStoreInitiative->assertRedirect();
        
        $newInitiative = Initiative::where('title', 'New Admin Initiative')->first();
        $this->assertNotNull($newInitiative);
        $this->assertNotNull($newInitiative->custom_fields);
        $this->assertEquals('School Name', $newInitiative->custom_fields[0]['label']);
        $this->assertEquals('school_name', $newInitiative->custom_fields[0]['name']);
        $this->assertTrue($newInitiative->custom_fields[0]['required']);

        // Superadmin can update an initiative with custom fields
        $responseUpdateInitiative = $this->actingAs($user)->put("/admin/structure/initiatives/{$newInitiative->id}", [
            'title' => 'Updated Admin Initiative',
            'description' => 'Updated program description.',
            'form_route' => 'forms.silid.create',
            'custom_fields' => [
                [
                    'label' => 'Age Limit',
                    'name' => 'age_limit',
                    'type' => 'number',
                    'placeholder' => 'Enter age...',
                    'required' => '0',
                ]
            ],
        ]);
        $responseUpdateInitiative->assertRedirect();
        $newInitiative->refresh();
        $this->assertEquals('Updated Admin Initiative', $newInitiative->title);
        $this->assertEquals('forms.silid.create', $newInitiative->form_route);
        $this->assertEquals('Age Limit', $newInitiative->custom_fields[0]['label']);
        $this->assertFalse($newInitiative->custom_fields[0]['required']);

        // Superadmin can archive (soft-delete) an initiative
        $responseDeleteInitiative = $this->actingAs($user)->delete("/admin/structure/initiatives/{$newInitiative->id}", [
            'password' => 'password',
        ]);
        $responseDeleteInitiative->assertRedirect();
        $this->assertSoftDeleted('initiatives', [
            'id' => $newInitiative->id,
        ]);

        // Superadmin can permanently delete the initiative from archive
        $responseForceDelete = $this->actingAs($user)->delete("/admin/structure/initiatives/{$newInitiative->id}/force-delete", [
            'password' => 'password',
        ]);
        $responseForceDelete->assertRedirect();
        $this->assertDatabaseMissing('initiatives', [
            'id' => $newInitiative->id,
        ]);

        // Superadmin can delete a committee (cascade delete verifies that child initiatives/reports are also deleted)
        $eduCommittee = Committee::where('slug', 'education')->first();
        $eduInitiatives = $eduCommittee->initiatives->pluck('id')->toArray();
        
        $responseDeleteCommittee = $this->actingAs($user)->delete("/admin/structure/committees/{$eduCommittee->id}", [
            'password' => 'password',
        ]);
        $responseDeleteCommittee->assertRedirect();
        
        $this->assertSoftDeleted('committees', ['id' => $eduCommittee->id]);
        foreach ($eduInitiatives as $id) {
            $this->assertSoftDeleted('initiatives', ['id' => $id]);
        }
    }
}
