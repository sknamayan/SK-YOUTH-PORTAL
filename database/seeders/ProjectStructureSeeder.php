<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Committee;
use App\Models\Initiative;
use App\Models\AccomplishmentReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProjectStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create main Project
        $project = Project::updateOrCreate(
            ['slug' => 'sk-namayan-youth-services'],
            [
                'title' => 'SK Namayan Youth Services',
                'description' => 'Comprehensive youth empowerment initiatives, community health programs, student research resources, and athletic leagues organized by the Sangguniang Kabataan of Barangay Namayan.'
            ]
        );

        // 2. Create Committees & Initiatives

        // 1. Education
        $edu = Committee::updateOrCreate(
            ['slug' => 'education', 'project_id' => $project->id],
            ['name' => 'Education']
        );

        // 2. Health & Wellness (Slug changed to 'health' for consistency)
        $health = Committee::updateOrCreate(
            ['slug' => 'health', 'project_id' => $project->id],
            ['name' => 'Health']
        );


        // 3. Governance
        $gov = Committee::updateOrCreate(
            ['slug' => 'governance', 'project_id' => $project->id],
            ['name' => 'Governance']
        );

        // 4. Active Citizenship
        $citizen = Committee::updateOrCreate(
            ['slug' => 'active-citizenship', 'project_id' => $project->id],
            ['name' => 'Active Citizenship']
        );

        // 5. Social Inclusion
        $social = Committee::updateOrCreate(
            ['slug' => 'social-inclusion', 'project_id' => $project->id],
            ['name' => 'Social Inclusion']
        );

        // 6. Peace Building
        $peace = Committee::updateOrCreate(
            ['slug' => 'peace-building', 'project_id' => $project->id],
            ['name' => 'Peace Building, Disaster Risk Reduction Management']
        );

        // 7. Environment
        $env = Committee::updateOrCreate(
            ['slug' => 'environment', 'project_id' => $project->id],
            ['name' => 'Environment']
        );


        // 8. Youth Employment
        $emp = Committee::updateOrCreate(
            ['slug' => 'youth-employment', 'project_id' => $project->id],
            ['name' => 'Youth Employment & Empowerment']
        );


        // 9. Agriculture
        $agri = Committee::updateOrCreate(
            ['slug' => 'agriculture', 'project_id' => $project->id],
            ['name' => 'Agriculture']
        );


        // 10. Global Mobility
        $mobility = Committee::updateOrCreate(
            ['slug' => 'global-mobility', 'project_id' => $project->id],
            ['name' => 'Global Mobility']
        );

        // Seed Silid Karunungan Studying Spaces Initiative
        Initiative::updateOrCreate(
            ['form_route' => 'forms.silid.create', 'committee_id' => $edu->id],
            [
                'title' => 'Silid Karunungan Studying Spaces',
                'description' => 'Reserve a study table and resource slot at the Silid Karunungan educational center.',
                'show_in_quick_forms' => true,
                'is_coming_soon' => false,
            ]
        );

        // Seed TTPD Printing Service Initiative
        Initiative::updateOrCreate(
            ['title' => 'TTPD Printing Service', 'committee_id' => $edu->id],
            [
                'description' => 'Free community printing services for students and researchers at the Silid Karunungan hub.',
                'form_route' => 'forms.silid.create',
                'show_in_quick_forms' => true,
                'is_coming_soon' => false,
            ]
        );
    }
}
