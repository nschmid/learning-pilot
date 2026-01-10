<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Zertifikat') }} - {{ $learningPath->title }}</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1a202c;
        }

        .certificate {
            width: 100%;
            height: 100%;
            padding: 40px;
            position: relative;
        }

        .certificate-inner {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px 60px;
            height: 100%;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .border-decoration {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid #e2e8f0;
            border-radius: 12px;
            pointer-events: none;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-area {
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 2px;
        }

        .certificate-title {
            font-size: 42px;
            font-weight: bold;
            color: #1a202c;
            margin: 20px 0 10px;
            letter-spacing: 3px;
        }

        .certificate-subtitle {
            font-size: 16px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .divider {
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 25px auto;
            border-radius: 2px;
        }

        .content {
            text-align: center;
            margin-bottom: 30px;
        }

        .presented-to {
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 20px;
        }

        .achievement-text {
            font-size: 16px;
            color: #4a5568;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 20px;
        }

        .course-title {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
        }

        .completion-date {
            font-size: 14px;
            color: #718096;
            margin-top: 20px;
        }

        .footer {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .footer-left,
        .footer-center,
        .footer-right {
            display: table-cell;
            vertical-align: bottom;
            width: 33.33%;
        }

        .footer-left {
            text-align: left;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
        }

        .signature-line {
            width: 180px;
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            margin: 0 auto;
        }

        .signature-name {
            font-size: 14px;
            font-weight: bold;
            color: #1a202c;
        }

        .signature-title {
            font-size: 12px;
            color: #718096;
        }

        .certificate-number {
            font-size: 11px;
            color: #a0aec0;
        }

        .qr-code {
            text-align: right;
        }

        .qr-code img {
            width: 80px;
            height: 80px;
        }

        .qr-label {
            font-size: 10px;
            color: #a0aec0;
            margin-top: 5px;
        }

        .validity {
            font-size: 11px;
            color: #a0aec0;
            margin-top: 5px;
        }

        .stats {
            display: table;
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            background: #f7fafc;
            border-radius: 8px;
            padding: 15px;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 0 15px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            font-size: 11px;
            color: #718096;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-inner">
            <div class="border-decoration"></div>

            <div class="header">
                <div class="logo-area">
                    <div class="company-name">LearningPilot</div>
                </div>
                <div class="certificate-title">{{ __('ZERTIFIKAT') }}</div>
                <div class="certificate-subtitle">{{ __('Abschlussbescheinigung') }}</div>
            </div>

            <div class="divider"></div>

            <div class="content">
                <div class="presented-to">{{ __('Hiermit wird bescheinigt, dass') }}</div>
                <div class="recipient-name">{{ $user->name }}</div>
                <div class="achievement-text">
                    {{ __('erfolgreich den Lernpfad abgeschlossen hat:') }}
                </div>
                <div class="course-title">{{ $learningPath->title }}</div>

                @if($metadata['points_earned'] > 0 || $metadata['total_time_spent'])
                <div class="stats">
                    @if($metadata['points_earned'] > 0)
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($metadata['points_earned']) }}</div>
                        <div class="stat-label">{{ __('Punkte') }}</div>
                    </div>
                    @endif
                    @if($metadata['total_time_spent'])
                    <div class="stat-item">
                        <div class="stat-value">{{ $metadata['total_time_spent'] }}</div>
                        <div class="stat-label">{{ __('Lernzeit') }}</div>
                    </div>
                    @endif
                </div>
                @endif

                <div class="completion-date">
                    {{ __('Abgeschlossen am') }} {{ $metadata['completed_at'] }}
                </div>
            </div>

            <div class="footer">
                <div class="footer-left">
                    <div class="certificate-number">
                        {{ __('Zertifikat-Nr.') }}: {{ $certificateNumber }}
                    </div>
                    @if($metadata['expires_at'])
                    <div class="validity">
                        {{ __('Gültig bis') }}: {{ $metadata['expires_at'] }}
                    </div>
                    @endif
                </div>

                <div class="footer-center">
                    <div class="signature-line">
                        <div class="signature-name">LearningPilot</div>
                        <div class="signature-title">{{ __('Plattformzertifizierung') }}</div>
                    </div>
                </div>

                <div class="footer-right">
                    @if($includeQrCode && $qrCodeUrl)
                    <div class="qr-code">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code">
                        <div class="qr-label">{{ __('Zur Verifizierung scannen') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
