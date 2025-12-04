<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credits laufen bald ab</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Credits laufen bald ab</h2>

        <p>Sehr geehrte Damen und Herren,</p>

        <p>einige Ihrer Credits werden in <strong>{{ $daysUntilExpiration }} {{ $daysUntilExpiration == 1 ? 'Tag' : 'Tagen' }}</strong> ablaufen.</p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #f39c12; margin: 20px 0;">
            <h3 style="margin-top: 0;">Zusammenfassung:</h3>
            <p style="margin: 5px 0;"><strong>Organisation/Einrichtung:</strong> {{ $creditable->name ?? $creditable->organization_name ?? 'Nicht verfügbar' }}</p>
            <p style="margin: 5px 0;"><strong>Anzahl ablaufender Credits:</strong> {{ $totalExpiringCredits }}</p>
            <p style="margin: 5px 0;"><strong>Aktuelles Guthaben:</strong> {{ $creditable->getCurrentCreditBalance() }}</p>
        </div>

        @if(count($expiringTransactions) > 0)
            <h3>Details zu den ablaufenden Credits:</h3>
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <thead>
                    <tr style="background-color: #e9ecef;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Kaufdatum</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Credits</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Ablaufdatum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringTransactions as $transaction)
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">{{ $transaction->created_at->format('d.m.Y') }}</td>
                            <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{ $transaction->amount }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">{{ $transaction->expires_at->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p><strong>Wichtig:</strong> Credits verfallen automatisch 3 Jahre nach dem Kaufdatum, wenn sie nicht eingelöst wurden. Nicht genutzte Credits werden vom Guthaben abgezogen.</p>

        <p>Nutzen Sie Ihre Credits rechtzeitig, um Stellenausschreibungen zu veröffentlichen oder zu verlängern.</p>

        <p style="margin-top: 30px;">
            <a href="{{ route('credits.index') }}" style="display: inline-block; padding: 12px 24px; background-color: #3490dc; color: #fff; text-decoration: none; border-radius: 5px;">Zum Credit-Bereich</a>
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.
        </p>

        <p style="font-size: 12px; color: #666;">
            Bei Fragen kontaktieren Sie uns bitte unter {{ config('mail.from.address') }}.
        </p>
    </div>
</body>
</html>

