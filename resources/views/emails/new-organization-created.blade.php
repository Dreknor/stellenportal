<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Organisation wartet auf Freischaltung</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Neue Organisation wartet auf Freischaltung</h2>

        <p>Hallo,</p>

        <p>eine neue Organisation wurde erstellt und wartet auf Ihre Freischaltung.</p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0;">
            <h3 style="margin-top: 0;">Organisationsdetails:</h3>
            <p style="margin: 5px 0;"><strong>Name:</strong> {{ $organization->name }}</p>
            @if($organization->email)
                <p style="margin: 5px 0;"><strong>E-Mail:</strong> {{ $organization->email }}</p>
            @endif
            @if($organization->phone)
                <p style="margin: 5px 0;"><strong>Telefon:</strong> {{ $organization->phone }}</p>
            @endif
            @if($organization->website)
                <p style="margin: 5px 0;"><strong>Website:</strong> {{ $organization->website }}</p>
            @endif
            @if($organization->address)
                <p style="margin: 5px 0;"><strong>Adresse:</strong>
                    {{ $organization->address->street }} {{ $organization->address->number }},
                    {{ $organization->address->zip_code }} {{ $organization->address->city }}
                </p>
            @endif
        </div>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #3490dc; margin: 20px 0;">
            <h3 style="margin-top: 0;">Erstellt von:</h3>
            <p style="margin: 5px 0;"><strong>Name:</strong> {{ $creator->name }}</p>
            <p style="margin: 5px 0;"><strong>E-Mail:</strong> {{ $creator->email }}</p>
        </div>

        @if($organization->description)
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="margin-top: 0;">Beschreibung:</h3>
                <p style="margin: 5px 0;">{{ $organization->description }}</p>
            </div>
        @endif

        <p style="margin-top: 30px;">
            <a href="{{ route('admin.organizations.show', $organization) }}" style="display: inline-block; padding: 12px 24px; background-color: #3490dc; color: #fff; text-decoration: none; border-radius: 5px;">Organisation prüfen und freischalten</a>
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.
        </p>
    </div>
</body>
</html>

