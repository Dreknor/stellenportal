<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ihre Zugangsdaten</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Willkommen bei {{ config('app.name') }}</h2>

        <p>Hallo {{ $user->first_name }} {{ $user->last_name }},</p>

        <p>ein Benutzerkonto wurde für Sie erstellt und Sie wurden der Einrichtung <strong>{{ $facility->name }}</strong> zugewiesen.</p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #10b981; margin: 20px 0;">
            <h3 style="margin-top: 0;">Ihre Zugangsdaten:</h3>
            <p style="margin: 5px 0;"><strong>E-Mail:</strong> {{ $user->email }}</p>
            <p style="margin: 5px 0;"><strong>Passwort:</strong> <code style="background-color: #f4f4f4; padding: 2px 6px; border-radius: 3px;">{{ $password }}</code></p>
        </div>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #3490dc; margin: 20px 0;">
            <h3 style="margin-top: 0;">Einrichtung:</h3>
            <p style="margin: 5px 0;"><strong>Name:</strong> {{ $facility->name }}</p>
            <p style="margin: 5px 0;"><strong>Träger:</strong> {{ $facility->organization->name }}</p>
            @if($facility->description)
            <p style="margin: 5px 0;"><strong>Beschreibung:</strong> {{ $facility->description }}</p>
            @endif
        </div>

        <p><strong>Wichtig:</strong> Sie erhalten eine separate E-Mail zur Bestätigung Ihrer E-Mail-Adresse. Bitte bestätigen Sie Ihre E-Mail-Adresse, bevor Sie sich anmelden.</p>

        <p><strong>Hinweis:</strong> Aus Sicherheitsgründen müssen Sie Ihr Passwort beim ersten Login ändern.</p>

        <p style="margin-top: 30px;">
            <a href="{{ route('login') }}" style="display: inline-block; padding: 12px 24px; background-color: #10b981; color: #fff; text-decoration: none; border-radius: 5px;">Jetzt anmelden</a>
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.
        </p>
    </div>
</body>
</html>

