<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geocodierung fehlgeschlagen</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #c0392b; margin-top: 0;">⚠️ Geocodierung fehlgeschlagen</h2>

        <p>Hallo,</p>

        <p>
            der tägliche Geocodierungs-Job hat für
            <strong>{{ count($failedAddresses) }} {{ count($failedAddresses) === 1 ? 'Adresse' : 'Adressen' }}</strong>
            keine Koordinaten ermitteln können.
            Bitte überprüfen Sie diese Adressen und korrigieren Sie sie ggf. manuell.
        </p>

        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #e74c3c; margin: 20px 0;">
            <h3 style="margin-top: 0;">Betroffene Adressen:</h3>

            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f8d7da;">
                        <th style="padding: 8px 10px; text-align: left; border: 1px solid #f5c6cb;">ID</th>
                        <th style="padding: 8px 10px; text-align: left; border: 1px solid #f5c6cb;">Straße / Nr.</th>
                        <th style="padding: 8px 10px; text-align: left; border: 1px solid #f5c6cb;">PLZ / Ort</th>
                        <th style="padding: 8px 10px; text-align: left; border: 1px solid #f5c6cb;">Zugehörigkeit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($failedAddresses as $address)
                        <tr style="{{ $loop->even ? 'background-color: #fafafa;' : '' }}">
                            <td style="padding: 8px 10px; border: 1px solid #ddd;">{{ $address->id }}</td>
                            <td style="padding: 8px 10px; border: 1px solid #ddd;">
                                {{ $address->street }} {{ $address->number }}
                            </td>
                            <td style="padding: 8px 10px; border: 1px solid #ddd;">
                                {{ $address->zip_code }} {{ $address->city }}
                            </td>
                            <td style="padding: 8px 10px; border: 1px solid #ddd;">
                                @if($address->addressable)
                                    {{ class_basename($address->addressable_type) }}:
                                    {{ $address->addressable->name ?? '–' }}
                                @else
                                    –
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p>
            Mögliche Ursachen für einen fehlgeschlagenen Geocodierungs-Versuch:
        </p>
        <ul style="margin: 0 0 20px 0; padding-left: 20px;">
            <li>Die Adresse ist unvollständig oder enthält Tippfehler.</li>
            <li>Die Adresse konnte vom Geocodierungs-Dienst nicht gefunden werden.</li>
            <li>Der API-Schlüssel ist abgelaufen oder das Anfragelimit wurde erreicht.</li>
        </ul>

        <p style="margin-top: 30px;">
            <a href="{{ config('app.url') }}/admin"
               style="display: inline-block; padding: 12px 24px; background-color: #c0392b; color: #fff; text-decoration: none; border-radius: 5px;">
                Zum Admin-Bereich
            </a>
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            Diese E-Mail wurde automatisch durch den täglichen Geocodierungs-Job generiert
            ({{ now()->format('d.m.Y H:i') }} Uhr).
            Bitte antworten Sie nicht auf diese E-Mail.
        </p>
    </div>
</body>
</html>

