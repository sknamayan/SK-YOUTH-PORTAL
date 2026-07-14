<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SK Namayan LGU Monthly Report — {{ $periodLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 12px; line-height: 1.5; }
        .header { border-bottom: 3px solid #1e40af; padding-bottom: 12px; margin-bottom: 24px; }
        .header h1 { color: #1e40af; font-size: 20px; margin: 0 0 4px; }
        .header p { margin: 0; color: #64748b; font-size: 11px; }
        .section { margin-bottom: 22px; }
        .section h2 { font-size: 14px; color: #1e40af; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px; }
        .stats-grid { width: 100%; border-collapse: collapse; }
        .stats-grid th, .stats-grid td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
        .stats-grid th { background: #eff6ff; color: #1e40af; font-size: 11px; text-transform: uppercase; }
        .metric { display: inline-block; width: 30%; margin-right: 2%; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px; vertical-align: top; }
        .metric strong { display: block; font-size: 22px; color: #1e40af; }
        .metric span { font-size: 10px; text-transform: uppercase; color: #64748b; }
        .footer { margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SK Namayan Youth Portal — LGU Monthly Report</h1>
        <p>Reporting Period: {{ $periodLabel }} | Generated: {{ $generatedAt->format('F j, Y g:i A') }}</p>
    </div>

    <div class="section">
        <h2>KK Profiling Overview</h2>
        <div>
            <div class="metric"><strong>{{ number_format($stats['total_youth']) }}</strong><span>Total Registered Youth</span></div>
            <div class="metric"><strong>{{ number_format($stats['sk_voters']) }}</strong><span>Registered SK Voters</span></div>
        </div>
    </div>

    <div class="section">
        <h2>Youth Classifications</h2>
        <table class="stats-grid">
            <thead><tr><th>Classification</th><th>Count</th></tr></thead>
            <tbody>
                @foreach($stats['youth_classifications'] as $label => $count)
                    <tr><td>{{ $label }}</td><td>{{ number_format($count) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Service Requests — {{ $periodLabel }}</h2>
        <table class="stats-grid">
            <thead>
                <tr>
                    <th>Program</th>
                    <th>Pending / Review</th>
                    <th>Approved</th>
                    <th>Declined</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['requests'] as $program => $counts)
                    <tr>
                        <td>{{ $program }}</td>
                        <td>{{ number_format($counts['pending']) }}</td>
                        <td>{{ number_format($counts['approved']) }}</td>
                        <td>{{ number_format($counts['declined']) }}</td>
                        <td>{{ number_format($counts['total']) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Totals</strong></td>
                    <td><strong>{{ number_format($stats['requests_pending_total']) }}</strong></td>
                    <td><strong>{{ number_format($stats['requests_approved_total']) }}</strong></td>
                    <td>—</td>
                    <td>—</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Confidential — For authorized LGU personnel only. Sangguniang Kabataan Barangay Namayan.
    </div>
</body>
</html>
