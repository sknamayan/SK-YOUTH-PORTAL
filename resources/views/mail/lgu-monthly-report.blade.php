<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #334155; line-height: 1.6;">
    <h2 style="color: #1e40af;">SK Namayan LGU Monthly Report</h2>
    <p>Good day,</p>
    <p>Please find attached the automated LGU monthly report for <strong>{{ $periodLabel }}</strong>.</p>

    <table style="border-collapse: collapse; margin: 16px 0; width: 100%; max-width: 420px;">
        <tr><td style="padding: 6px 0; color: #64748b;">Total Registered Youth</td><td style="padding: 6px 0; font-weight: bold;">{{ number_format($stats['total_youth']) }}</td></tr>
        <tr><td style="padding: 6px 0; color: #64748b;">Pending Requests (Period)</td><td style="padding: 6px 0; font-weight: bold;">{{ number_format($stats['requests_pending_total']) }}</td></tr>
        <tr><td style="padding: 6px 0; color: #64748b;">Approved Requests (Period)</td><td style="padding: 6px 0; font-weight: bold;">{{ number_format($stats['requests_approved_total']) }}</td></tr>
    </table>

    <p style="font-size: 13px; color: #64748b;">This report was generated automatically by the SK Youth Portal.</p>
    <p>— SK Namayan Youth Services</p>
</body>
</html>
