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
        $silid = Initiative::updateOrCreate(
            ['title' => 'Silid Karunungan Booking', 'committee_id' => $edu->id],
            [
                'description' => 'A dedicated digital research room and quiet study hub provided to local youth, offering stable internet connections, research databases, and reading areas.',
                'form_route' => 'forms.silid.create'
            ]
        );
        $tipd = Initiative::updateOrCreate(
            ['title' => 'TIPD Scholarship Program', 'committee_id' => $edu->id],
            [
                'description' => 'Tertiary Education Subsidy and scholarship mapping for deserving youth residents of Barangay Namayan.',
                'form_route' => null
            ]
        );
        $als = Initiative::updateOrCreate(
            ['title' => 'Alternative Learning System', 'committee_id' => $edu->id],
            [
                'description' => 'Alternative basic education program designed for out-of-school youths and adult learners.',
                'form_route' => null
            ]
        );

        // 2. Health & Wellness (Slug changed to 'health' for consistency)
        $health = Committee::updateOrCreate(
            ['slug' => 'health', 'project_id' => $project->id],
            ['name' => 'Health']
        );
        $consult = Initiative::updateOrCreate(
            ['title' => 'Health Consultation', 'committee_id' => $health->id],
            [
                'description' => 'Free physical medical guidance, diagnostic check-ups, and pediatric assessment requests arranged with licensed physicians.',
                'form_route' => 'forms.health.create'
            ]
        );
        $mental = Initiative::updateOrCreate(
            ['title' => 'Mental Health Support', 'committee_id' => $health->id],
            [
                'description' => 'Professional counseling services, stress management sessions, and supportive guidance coordinates to empower youth mental health.',
                'form_route' => 'forms.mental-health.create'
            ]
        );
        $sportsLeague = Initiative::updateOrCreate(
            ['title' => 'Sports League Registration', 'committee_id' => $health->id],
            [
                'description' => 'Inter-barangay athletic tournaments, basketball leagues, volleyball matches, and physical fitness clinics held annually.',
                'form_route' => 'forms.sports.create'
            ]
        );

        // 3. Governance
        $gov = Committee::updateOrCreate(
            ['slug' => 'governance', 'project_id' => $project->id],
            ['name' => 'Governance']
        );
        $assembly = Initiative::updateOrCreate(
            ['title' => 'Sangguniang Kabataan Assembly', 'committee_id' => $gov->id],
            [
                'description' => 'Regular consultative general assembly for SK policies, resolution hearings, and community updates.',
                'form_route' => null
            ]
        );
        $tracker = Initiative::updateOrCreate(
            ['title' => 'Legislative Tracker', 'committee_id' => $gov->id],
            [
                'description' => 'Monitor resolutions, action plans, ordinances, and youth welfare bills drafted by the SK council.',
                'form_route' => null
            ]
        );

        // 4. Active Citizenship
        $citizen = Committee::updateOrCreate(
            ['slug' => 'active-citizenship', 'project_id' => $project->id],
            ['name' => 'Active Citizenship']
        );
        $volunteer = Initiative::updateOrCreate(
            ['title' => 'Youth Volunteer Corps', 'committee_id' => $citizen->id],
            [
                'description' => 'Join community services, relief operations, leadership camps, and civic duty tasks within Mandaluyong.',
                'form_route' => null
            ]
        );

        // 5. Social Inclusion
        $social = Committee::updateOrCreate(
            ['slug' => 'social-inclusion', 'project_id' => $project->id],
            ['name' => 'Social Inclusion']
        );
        $medicine = Initiative::updateOrCreate(
            ['title' => 'Pabili Medicine Services', 'committee_id' => $social->id],
            [
                'description' => 'Subsidized medicine purchasing assistance and prescription fulfillment services delivered straight to youth households.',
                'form_route' => 'forms.medicine.create'
            ]
        );
        $accessibility = Initiative::updateOrCreate(
            ['title' => 'Accessibility Aid Request', 'committee_id' => $social->id],
            [
                'description' => 'Assistance desk and custom aid requests for youth members with special needs or disabilities.',
                'form_route' => null
            ]
        );

        // 6. Peace Building
        $peace = Committee::updateOrCreate(
            ['slug' => 'peace-building', 'project_id' => $project->id],
            ['name' => 'Peace Building']
        );
        $resolution = Initiative::updateOrCreate(
            ['title' => 'Conflict Resolution Desk', 'committee_id' => $peace->id],
            [
                'description' => 'Mediation desk, anti-drug advocacy programs, and local counseling for minor neighborhood disputes.',
                'form_route' => null
            ]
        );

        // 7. Environment
        $env = Committee::updateOrCreate(
            ['slug' => 'environment', 'project_id' => $project->id],
            ['name' => 'Environment']
        );
        $eco = Initiative::updateOrCreate(
            ['title' => 'Eco-Warriors Volunteer Group', 'committee_id' => $env->id],
            [
                'description' => 'Tree planting, cleanup drives, recycling campaigns, and community disaster response simulation drills.',
                'form_route' => null
            ]
        );

        // 8. Youth Employment
        $emp = Committee::updateOrCreate(
            ['slug' => 'youth-employment', 'project_id' => $project->id],
            ['name' => 'Youth Employment & Empowerment']
        );
        $internship = Initiative::updateOrCreate(
            ['title' => 'Internship Portal', 'committee_id' => $emp->id],
            [
                'description' => 'Connecting undergraduate students and vocational trainees with local partner companies and public offices.',
                'form_route' => null
            ]
        );
        $likha = Initiative::updateOrCreate(
            ['title' => 'SK Likha Livelihood Workshops', 'committee_id' => $emp->id],
            [
                'description' => 'Skills trainings, graphic design bootcamps, and entrepreneurship starter funding for young businesses.',
                'form_route' => null
            ]
        );

        // 9. Agriculture
        $agri = Committee::updateOrCreate(
            ['slug' => 'agriculture', 'project_id' => $project->id],
            ['name' => 'Agriculture']
        );
        $agriHub = Initiative::updateOrCreate(
            ['title' => 'Kabataang Agri-Pins Hub', 'committee_id' => $agri->id],
            [
                'description' => 'Urban gardening seminars, hydroponics distribution, and community farm plots managed by local youth.',
                'form_route' => null
            ]
        );

        // 10. Global Mobility
        $mobility = Committee::updateOrCreate(
            ['slug' => 'global-mobility', 'project_id' => $project->id],
            ['name' => 'Global Mobility']
        );
        $scholarship = Initiative::updateOrCreate(
            ['title' => 'Scholarship Verification Desk', 'committee_id' => $mobility->id],
            [
                'description' => 'Assistance with municipal scholarship requirements, endorsement letters, and document verifications.',
                'form_route' => null
            ]
        );


        // 3. Seed Accomplishment Reports
        AccomplishmentReport::updateOrCreate(
            ['report_title' => 'Q1 Silid Karunungan Booking Attendance Report', 'initiative_id' => $silid->id],
            [
                'file_path' => 'reports/silid_karunungan_q1_2026.pdf',
                'reporting_period' => Carbon::parse('2026-03-31')
            ]
        );
        AccomplishmentReport::updateOrCreate(
            ['report_title' => 'Annual SK Summer League Final Report', 'initiative_id' => $sportsLeague->id],
            [
                'file_path' => 'reports/summer_league_final_2026.pdf',
                'reporting_period' => Carbon::parse('2026-05-31')
            ]
        );
        AccomplishmentReport::updateOrCreate(
            ['report_title' => 'Q1 Community Medicine Distribution Summary', 'initiative_id' => $medicine->id],
            [
                'file_path' => 'reports/medicine_distribution_q1.pdf',
                'reporting_period' => Carbon::parse('2026-03-31')
            ]
        );
        AccomplishmentReport::updateOrCreate(
            ['report_title' => 'SK Namayan Youth Assembly Accomplishment Audit', 'initiative_id' => $assembly->id],
            [
                'file_path' => 'reports/youth_assembly_audit.pdf',
                'reporting_period' => Carbon::parse('2026-04-15')
            ]
        );
        AccomplishmentReport::updateOrCreate(
            ['report_title' => 'Barangay Namayan Green Environment Cleanup Report', 'initiative_id' => $eco->id],
            [
                'file_path' => 'reports/eco_warriors_cleanup.pdf',
                'reporting_period' => Carbon::parse('2026-05-01')
            ]
        );
    }
}
