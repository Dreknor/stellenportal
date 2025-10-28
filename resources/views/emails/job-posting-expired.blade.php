<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stellenausschreibung abgelaufen</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Stellenausschreibung abgelaufen</h2>

        <p>Hallo {{ $user->first_name }} {{ $user->last_name }},</p>

        <p>Ihre Stellenausschreibung ist abgelaufen und wurde automatisch deaktiviert.</p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #e74c3c; margin: 20px 0;">
            <h3 style="margin-top: 0;">Details zur Stellenausschreibung:</h3>
            <p style="margin: 5px 0;"><strong>Titel:</strong> {{ $jobPosting->title }}</p>
            <p style="margin: 5px 0;"><strong>Einrichtung:</strong> {{ $jobPosting->facility->name }}</p>
            <p style="margin: 5px 0;"><strong>Veröffentlicht am:</strong> {{ $jobPosting->published_at?->format('d.m.Y') }}</p>
            <p style="margin: 5px 0;"><strong>Abgelaufen am:</strong> {{ $jobPosting->expires_at?->format('d.m.Y') }}</p>
        </div>

        <p>Möchten Sie diese Stellenausschreibung verlängern? Sie können die Stellenausschreibung einfach mit zusätzlichen Credits reaktivieren.</p>

        <p style="margin-top: 30px;">
            <a href="{{ route('job-postings.show', $jobPosting) }}" style="display: inline-block; padding: 12px 24px; background-color: #3490dc; color: #fff; text-decoration: none; border-radius: 5px;">Stellenausschreibung anzeigen</a>
        </p>

        <p style="margin-top: 15px;">
            <a href="{{ route('job-postings.index') }}" style="display: inline-block; padding: 12px 24px; background-color: #38c172; color: #fff; text-decoration: none; border-radius: 5px;">Zu meinen Stellenausschreibungen</a>
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

