<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Willkommen - Ihre Zugangsdaten</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Willkommen bei {{ config('app.name') }}</h2>

        <p>Hallo {{ $user->first_name }} {{ $user->last_name }},</p>

        <p>ein Administrator hat ein Benutzerkonto für Sie im {{ config('app.name') }} System erstellt.</p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #3490dc; margin: 20px 0;">
            <h3 style="margin-top: 0;">Ihre Zugangsdaten:</h3>
            <p style="margin: 5px 0;"><strong>E-Mail:</strong> {{ $user->email }}</p>
            <p style="margin: 5px 0;"><strong>Passwort:</strong> <code style="background-color: #f4f4f4; padding: 2px 6px; border-radius: 3px;">{{ $password }}</code></p>
        </div>

        @if($user->organizations->count() > 0)
        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #38c172; margin: 20px 0;">
            <h3 style="margin-top: 0;">Ihre zugewiesenen Organisationen:</h3>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @foreach($user->organizations as $organization)
                <li>{{ $organization->name }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($user->facilities->count() > 0)
        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #f6993f; margin: 20px 0;">
            <h3 style="margin-top: 0;">Ihre zugewiesenen Einrichtungen:</h3>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @foreach($user->facilities as $facility)
                <li>{{ $facility->name }} ({{ $facility->organization->name }})</li>
                @endforeach
            </ul>
        </div>
        @endif

        <p><strong>Wichtig:</strong> Sie erhalten eine separate E-Mail zur Bestätigung Ihrer E-Mail-Adresse. Bitte bestätigen Sie Ihre E-Mail-Adresse, bevor Sie sich anmelden.</p>

        <p><strong>Hinweis:</strong> Bitte ändern Sie Ihr Passwort bei der ersten Anmeldung.</p>

        <p style="margin-top: 30px;">
            <a href="{{ route('login') }}" style="display: inline-block; padding: 12px 24px; background-color: #3490dc; color: #fff; text-decoration: none; border-radius: 5px;">Jetzt anmelden</a>
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.
        </p>
    </div>
</body>
</html>

