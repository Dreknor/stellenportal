@php
    $levelColors = [
        'EMERGENCY' => '#991B1B',
        'ALERT' => '#DC2626',
        'CRITICAL' => '#EA580C',
        'ERROR' => '#F59E0B',
        'WARNING' => '#EAB308',
    ];
    $color = $levelColors[$log->level_name] ?? '#6B7280';
@endphp

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kritischer Log-Eintrag</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: {{ $color }}; color: white; padding: 20px; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">
            @if($log->level_name === 'EMERGENCY')
                🚨 NOTFALL
            @elseif($log->level_name === 'ALERT')
                🔴 ALARM
            @elseif($log->level_name === 'CRITICAL')
                ⚠️ KRITISCH
            @elseif($log->level_name === 'ERROR')
                ❌ FEHLER
            @else
                ⚠️ WARNUNG
            @endif
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">{{ config('app.name') }} - Kritischer Log-Eintrag</p>
    </div>

    <div style="background-color: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <div style="background-color: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #374151;">Log-Details</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #6b7280; width: 120px; vertical-align: top;"><strong>Level:</strong></td>
                    <td style="padding: 8px 0;">
                        <span style="background-color: {{ $color }}; color: white; padding: 4px 12px; border-radius: 4px; font-weight: bold;">
                            {{ $log->level_name }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280; vertical-align: top;"><strong>Zeitpunkt:</strong></td>
                    <td style="padding: 8px 0;">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280; vertical-align: top;"><strong>Kanal:</strong></td>
                    <td style="padding: 8px 0;">{{ $log->channel ?? 'N/A' }}</td>
                </tr>
                @if($log->context && isset($log->context['user_id']))
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; vertical-align: top;"><strong>Benutzer-ID:</strong></td>
                        <td style="padding: 8px 0;">{{ $log->context['user_id'] }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div style="background-color: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #374151;">Nachricht</h2>
            <p style="margin: 0; padding: 12px; background-color: #f3f4f6; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 14px; white-space: pre-wrap; word-break: break-word;">{{ $log->message }}</p>
        </div>

        @if($log->context && isset($log->context['exception']))
            @php
                $exception = $log->context['exception'];
            @endphp
            <div style="background-color: #fef2f2; padding: 15px; border-radius: 6px; border-left: 4px solid #DC2626; margin-bottom: 15px;">
                <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #991B1B;">Exception Details</h2>
                @if(is_object($exception))
                    @if(method_exists($exception, 'getMessage'))
                        <p style="margin: 0 0 8px 0;"><strong>Message:</strong> {{ $exception->getMessage() }}</p>
                    @endif
                    @if(method_exists($exception, 'getFile'))
                        <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>File:</strong><br>
                            <code style="background-color: #fee2e2; padding: 2px 6px; border-radius: 3px;">{{ $exception->getFile() }}</code>
                        </p>
                    @endif
                    @if(method_exists($exception, 'getLine'))
                        <p style="margin: 0; font-size: 13px;"><strong>Line:</strong> {{ $exception->getLine() }}</p>
                    @endif
                @endif
            </div>
        @endif

        @if($log->context && !empty($log->context))
            <div style="background-color: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #374151;">Context</h2>
                <pre style="margin: 0; padding: 12px; background-color: #f3f4f6; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 12px; overflow-x: auto; white-space: pre-wrap; word-break: break-word;">{{ $log->formatted_context }}</pre>
            </div>
        @endif

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('admin.logs.show', $log->id) }}"
               style="display: inline-block; background-color: #3B82F6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Details im Adminbereich anzeigen
            </a>
        </div>

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 12px;">
            <p style="margin: 0;">
                Diese E-Mail wurde automatisch generiert.<br>
                Log-Eintrag ID: #{{ $log->id }} | {{ config('app.name') }}
            </p>
        </div>
    </div>
</body>
</html>

