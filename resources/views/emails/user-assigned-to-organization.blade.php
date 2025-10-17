<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organisation zugewiesen</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Neue Organisationszuweisung</h2>

        <p>Hallo {{ $user->first_name }} {{ $user->last_name }},</p>

        <p>Sie wurden der Organisation <strong>{{ $organization->name }}</strong> zugewiesen.</p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #38c172; margin: 20px 0;">
            <h3 style="margin-top: 0;">Organisation:</h3>
            <p style="margin: 5px 0;"><strong>Name:</strong> {{ $organization->name }}</p>
            @if($organization->description)
            <p style="margin: 5px 0;"><strong>Beschreibung:</strong> {{ $organization->description }}</p>
            @endif
        </div>

        <p>Sie können sich nun mit Ihren bestehenden Zugangsdaten anmelden und auf die Funktionen dieser Organisation zugreifen.</p>

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

