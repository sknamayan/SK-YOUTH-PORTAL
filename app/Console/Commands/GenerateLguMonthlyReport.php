<?php

namespace App\Console\Commands;

use App\Mail\LguMonthlyReportMail;
use App\Models\CustomRequest;
use App\Models\HealthRequest;
use App\Models\KkProfile;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class GenerateLguMonthlyReport extends Command
{
    protected $signature = 'sk:generate-lgu-monthly-report {--month= : Report month (Y-m), defaults to previous month} {--email= : Override recipient email}';

    protected $description = 'Aggregate LGU youth statistics and email a PDF report to administrators';

    public function handle(): int
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $this->error('barryvdh/laravel-dompdf is not installed. Run: composer require barryvdh/laravel-dompdf');

            return self::FAILURE;
        }

        $month = $this->option('month')
            ? \Carbon\Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth()
            : now()->subMonth()->startOfMonth();

        $periodLabel = $month->format('F Y');
        $stats = $this->aggregateStatistics($month);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.lgu-monthly', [
            'periodLabel' => $periodLabel,
            'generatedAt' => now(),
            'stats' => $stats,
        ])->setPaper('a4', 'portrait');

        $filename = 'SK-Namayan-LGU-Report-' . $month->format('Y-m') . '.pdf';
        $pdfPath = storage_path('app/reports/' . $filename);

        if (!is_dir(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        $pdf->save($pdfPath);

        $recipients = $this->resolveRecipients();
        if ($recipients->isEmpty()) {
            $this->warn('No admin recipients found. PDF saved to: ' . $pdfPath);

            return self::SUCCESS;
        }

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->queue(new LguMonthlyReportMail($periodLabel, $pdfPath, $stats));
                $this->info('Queued report email to: ' . $email);
            } catch (\Throwable $e) {
                $this->error('Failed to queue report for ' . $email . ': ' . $e->getMessage());
            }
        }

        $this->info('LGU monthly report generated for ' . $periodLabel);

        return self::SUCCESS;
    }

    private function aggregateStatistics(\Carbon\Carbon $month): array
    {
        $year = $month->year;
        $monthNum = $month->month;

        $youthClassifications = [
            'ISY' => KkProfile::where('youth_classification', 'ISY')->count(),
            'OSY' => KkProfile::where('youth_classification', 'OSY')->count(),
            'WY' => KkProfile::where('youth_classification', 'WY')->count(),
        ];

        $requestTypes = [
            'Health' => HealthRequest::class,
            'Medicine' => MedicineRequest::class,
            'Silid Karunungan' => SilidKarununganRequest::class,
            'Sports' => SportsRegistration::class,
            'Custom' => CustomRequest::class,
        ];

        $requests = [];
        foreach ($requestTypes as $label => $modelClass) {
            $base = $modelClass::whereYear('created_at', $year)->whereMonth('created_at', $monthNum);
            $requests[$label] = [
                'pending' => (clone $base)->whereIn('status', ['pending', 'review'])->count(),
                'approved' => (clone $base)->where('status', 'approved')->count(),
                'declined' => (clone $base)->where('status', 'declined')->count(),
                'total' => (clone $base)->count(),
            ];
        }

        return [
            'total_youth' => KkProfile::count(),
            'sk_voters' => KkProfile::where('registered_sk_voter', true)->count(),
            'youth_classifications' => $youthClassifications,
            'requests' => $requests,
            'requests_pending_total' => collect($requests)->sum('pending'),
            'requests_approved_total' => collect($requests)->sum('approved'),
        ];
    }

    private function resolveRecipients()
    {
        if ($email = $this->option('email')) {
            return collect([$email]);
        }

        return User::where('role', 'superadmin')
            ->where('is_approved', true)
            ->pluck('email');
    }
}
