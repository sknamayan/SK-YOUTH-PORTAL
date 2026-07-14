<?php

namespace Database\Seeders;

use App\Models\SkOfficial;
use App\Models\TransparencyPost;
use Illuminate\Database\Seeder;

class GovernanceSeeder extends Seeder
{
    public function run(): void
    {
        $officials = [
            [
                'name' => 'Maria Isabel Santos',
                'position' => 'SK Chairperson',
                'sort_order' => 1,
                'term' => '2023 – 2026',
                'email' => 'chairperson@sknamayan.gov.ph',
                'contact_number' => '+63 917 123 4567',
                'bio' => "Maria Isabel Santos serves as the SK Chairperson of Barangay Namayan, Mandaluyong City. She leads the Sangguniang Kabataan in implementing youth-centered programs focused on education, health, and active citizenship.\n\nUnder her leadership, SK Namayan launched the digital request portal to make barangay services more accessible to young residents. She is committed to open governance, regular KK assemblies, and transparent use of SK funds.",
            ],
            [
                'name' => 'John Michael Reyes',
                'position' => 'SK Vice Chairperson',
                'sort_order' => 2,
                'term' => '2023 – 2026',
                'email' => 'vicechair@sknamayan.gov.ph',
                'contact_number' => '+63 918 234 5678',
                'bio' => "John Michael Reyes supports the Chairperson in presiding over SK sessions and coordinating inter-committee projects. He spearheads sports development and youth employment initiatives, including the annual Inter-Purok Sports League and skills training partnerships with local NGOs.",
            ],
            [
                'name' => 'Angela Mae Cruz',
                'position' => 'SK Secretary',
                'sort_order' => 3,
                'term' => '2023 – 2026',
                'email' => 'secretary@sknamayan.gov.ph',
                'contact_number' => '+63 919 345 6789',
                'bio' => "Angela Mae Cruz maintains official SK records, prepares meeting minutes, and manages correspondence with the Barangay Council and city youth office. She ensures all resolutions and ordinances are properly documented and posted on the public transparency board.",
            ],
            [
                'name' => 'Carlo Antonio Mendoza',
                'position' => 'SK Treasurer',
                'sort_order' => 4,
                'term' => '2023 – 2026',
                'email' => 'treasurer@sknamayan.gov.ph',
                'contact_number' => '+63 920 456 7890',
                'bio' => "Carlo Antonio Mendoza oversees the collection, disbursement, and reporting of SK funds in compliance with COA and DILG guidelines. He publishes quarterly financial statements and works closely with the SK Chairperson on budget planning and project fund allocation.",
            ],
            [
                'name' => 'Patricia Louise Lim',
                'position' => 'SK Councilor – Education & Youth Development',
                'sort_order' => 5,
                'term' => '2023 – 2026',
                'email' => 'councilor.education@sknamayan.gov.ph',
                'contact_number' => '+63 921 567 8901',
                'bio' => "Patricia Louise Lim chairs the Education Committee and manages the Silid Karunungan studying hub program. She organizes review sessions, scholarship referral drives, and literacy campaigns for out-of-school youth in Barangay Namayan.",
            ],
            [
                'name' => 'Rafael Miguel Torres',
                'position' => 'SK Councilor – Health & Wellness',
                'sort_order' => 6,
                'term' => '2023 – 2026',
                'email' => 'councilor.health@sknamayan.gov.ph',
                'contact_number' => '+63 922 678 9012',
                'bio' => "Rafael Miguel Torres leads health consultation drives, mental health awareness campaigns, and the Pabili Medicine assistance program. He coordinates with barangay health workers and youth volunteers for community wellness activities.",
            ],
            [
                'name' => 'Sophia Danielle Garcia',
                'position' => 'SK Councilor – Environment & Livelihood',
                'sort_order' => 7,
                'term' => '2023 – 2026',
                'email' => 'councilor.environment@sknamayan.gov.ph',
                'contact_number' => '+63 923 789 0123',
                'bio' => "Sophia Danielle Garcia promotes environmental stewardship through tree planting, clean-up drives, and waste reduction programs. She also supports livelihood seminars and eco-friendly small business initiatives for young entrepreneurs.",
            ],
            [
                'name' => 'Mark Anthony Villanueva',
                'position' => 'SK Councilor – Sports & Active Citizenship',
                'sort_order' => 8,
                'term' => '2023 – 2026',
                'email' => 'councilor.sports@sknamayan.gov.ph',
                'contact_number' => '+63 924 890 1234',
                'bio' => "Mark Anthony Villanueva organizes barangay sports leagues, esports tournaments, and civic engagement activities. He encourages youth participation in governance through KK assemblies and barangay volunteer programs.",
            ],
        ];

        foreach ($officials as $data) {
            SkOfficial::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }

        $transparencyPosts = [
            [
                'title' => 'SK Namayan Annual Budget Appropriation FY 2025',
                'category' => 'budget',
                'excerpt' => 'Approved annual budget and line-item appropriations for SK Namayan programs covering health, education, sports, and governance for fiscal year 2025.',
                'content' => "Pursuant to Section 23 of the SK Reform Act (RA 10742), the Sangguniang Kabataan of Barangay Namayan hereby publishes its approved Annual Budget for FY 2025.\n\nKey allocations include:\n• Youth Health & Wellness Programs — 25%\n• Education & Silid Karunungan — 30%\n• Sports Development & Recreation — 20%\n• Governance, Training & Admin — 15%\n• Environment & Livelihood — 10%\n\nThis budget was approved during the SK General Assembly held on January 15, 2025, and is subject to COA audit guidelines.",
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Quarterly Financial Report — Q1 2025',
                'category' => 'financial',
                'excerpt' => 'Statement of receipts and disbursements for the first quarter of 2025, including project expenditures and remaining fund balances.',
                'content' => "This report covers all SK fund transactions from January 1 to March 31, 2025.\n\nTotal receipts: PHP 485,000.00\nTotal disbursements: PHP 312,450.00\nEnding balance: PHP 172,550.00\n\nMajor disbursements were made for the Silid Karunungan renovation, sports league equipment, and health consultation supplies. Full supporting documents are available upon request at the SK office.",
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'SK Resolution No. 2025-01 — Inter-Barangay Youth Sports League',
                'category' => 'resolution',
                'excerpt' => 'Resolution authorizing the conduct and funding of the SK Namayan Inter-Purok Youth Sports League 2025.',
                'content' => "WHEREAS, sports promote discipline, teamwork, and healthy lifestyles among the youth;\n\nWHEREAS, the SK aims to provide structured recreational activities for residents aged 15–30;\n\nNOW, THEREFORE, BE IT RESOLVED to authorize the SK Sports Committee to conduct the Inter-Purok Youth Sports League from April to June 2025, with basketball, volleyball, and badminton categories.",
                'published_at' => now()->subDays(21),
            ],
            [
                'title' => 'SK Resolution No. 2025-02 — Silid Karunungan Operating Hours',
                'category' => 'resolution',
                'excerpt' => 'Resolution establishing official operating hours and usage guidelines for the Silid Karunungan community study hub.',
                'content' => "The SK resolves to set Silid Karunungan operating hours as Monday–Friday, 8:00 AM to 8:00 PM, and Saturday, 9:00 AM to 5:00 PM. Booking is required through the online portal. Priority is given to students preparing for board exams and college entrance tests.",
                'published_at' => now()->subDays(28),
            ],
            [
                'title' => '2024 Year-End Accomplishment Report',
                'category' => 'accomplishment',
                'excerpt' => 'Comprehensive summary of SK Namayan projects, events, and outcomes delivered throughout calendar year 2024.',
                'content' => "Highlights from 2024:\n\n✓ 12 health consultation drives serving 340+ youth\n✓ Silid Karunungan opened with 50 daily average users\n✓ 3 sports tournaments with 24 participating teams\n✓ 2 tree planting activities (500 seedlings)\n✓ 8 KK assemblies with 200+ attendees\n✓ Digital request portal launched\n\nFull narrative and photo documentation available in the attached report.",
                'published_at' => now()->subDays(45),
            ],
            [
                'title' => 'Public Notice — KK Assembly Schedule (June 2025)',
                'category' => 'announcement',
                'excerpt' => 'Official notice of the upcoming Katipunan ng Kabataan General Assembly on June 28, 2025 at the Barangay Covered Court.',
                'content' => "All registered SK voters and youth residents aged 15–30 are invited to attend the KK General Assembly.\n\nDate: June 28, 2025\nTime: 2:00 PM\nVenue: Barangay Namayan Covered Court\n\nAgenda: FY 2025 mid-year report, sports league updates, and open forum. Valid ID required for attendance registration.",
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'COA Compliance Checklist — SK Fund Utilization 2024',
                'category' => 'financial',
                'excerpt' => 'Published checklist documenting SK Namayan compliance with Commission on Audit requirements for youth fund management.',
                'content' => "This disclosure confirms that SK Namayan has submitted all required documents to the City Youth Office and Barangay Council, including liquidation reports, official receipts, and project completion certificates for all funded activities in 2024.",
                'published_at' => now()->subDays(60),
            ],
            [
                'title' => 'SK Ordinance No. 2024-03 — Anti-Bullying Youth Policy',
                'category' => 'resolution',
                'excerpt' => 'Local SK ordinance establishing anti-bullying guidelines and reporting mechanisms for youth in Barangay Namayan.',
                'content' => "The SK enacts this ordinance to protect youth from bullying in schools and community spaces. A reporting hotline and confidential referral process to barangay social workers and mental health support is established.",
                'published_at' => now()->subDays(90),
            ],
        ];

        foreach ($transparencyPosts as $data) {
            TransparencyPost::updateOrCreate(
                ['title' => $data['title']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
