<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SK Portal Email</title>
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }
        .header {
            background-color: #1e40af;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header img {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
            font-size: 14px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .btn {
            display: inline-block;
            background-color: #1e40af;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }
        .reference-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .reference-title {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 4px;
        }
        .reference-number {
            font-size: 26px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 800;
            color: #1e40af;
            margin-top: 4px;
            margin-bottom: 4px;
            display: block;
            letter-spacing: 0.5px;
        }
        .badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .badge-review {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .badge-approved {
            background-color: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .badge-declined {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" class="w-10 h-10 object-contain rounded-full bg-white p-0.5 border border-blue-200 shadow-sm transition group-hover:scale-105" alt="SK Logo">
            <h1>Sangguniang Kabataan</h1>
            <p>Barangay Namayan Portal</p>
        </div>
        @yield('content')
        <div class="footer">
            Sangguniang Kabataan Barangay Namayan &bull; Mandaluyong City, Metro Manila<br>
            Please do not reply directly to this email. For inquiries, email info@sknamayan.gov.ph.
        </div>
    </div>
</body>
</html>
