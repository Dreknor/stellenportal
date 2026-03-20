<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organisation freigeschaltet</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Ihre Organisation wurde freigeschaltet</h2>

        <p>Hallo {{ $user->first_name }} {{ $user->last_name }},</p>

        <p>wir freuen uns, Ihnen mitteilen zu können, dass Ihre Organisation erfolgreich überprüft und freigeschaltet wurde.</p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #38c172; margin: 20px 0;">
            <h3 style="margin-top: 0;">Freigeschaltete Organisation:</h3>
            <p style="margin: 5px 0;"><strong>{{ $organization->name }}</strong></p>
        </div>

        <p>Ab sofort stehen Ihnen alle Funktionen des Portals zur Verfügung:</p>

        <ul style="padding-left: 20px;">
            <li>Einrichtungen anlegen und verwalten</li>
            <li>Stellenausschreibungen erstellen und veröffentlichen</li>
            <li>Credits erwerben und verwalten</li>
        </ul>

        <p style="margin-top: 30px;">
            <a href="{{ route('dashboard') }}" style="display: inline-block; padding: 12px 24px; background-color: #38c172; color: #fff; text-decoration: none; border-radius: 5px;">Zum Dashboard</a>
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

