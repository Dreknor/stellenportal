<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Hilfe-Anfrage') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3B82F6;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .message-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin: 20px 0;
        }
        .info-row {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #6b7280;
            display: inline-block;
            min-width: 120px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">📧 Neue Hilfe-Anfrage</h1>
    </div>

    <div class="content">
        <p><strong>Sie haben eine neue Anfrage über das Hilfe-Kontaktformular erhalten.</strong></p>

        <div class="message-box">
            <div class="info-row">
                <span class="label">Von:</span>
                <span>{{ $senderName }}</span>
            </div>
            <div class="info-row">
                <span class="label">E-Mail:</span>
                <span><a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></span>
            </div>
            @if($userId)
            <div class="info-row">
                <span class="label">Benutzer-ID:</span>
                <span>{{ $userId }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Betreff:</span>
                <span>{{ $mailSubject }}</span>
            </div>
        </div>

        <div class="message-box">
            <p style="margin-top: 0;"><strong>Nachricht:</strong></p>
            <div style="white-space: pre-wrap; word-wrap: break-word;">{{ $messageContent }}</div>
        </div>

        <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
            Diese E-Mail wurde automatisch über das Hilfe-Kontaktformular von {{config('app.name')}} gesendet.
            Bitte antworten Sie direkt an die oben angegebene E-Mail-Adresse.
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{config('app.name')}} Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>

