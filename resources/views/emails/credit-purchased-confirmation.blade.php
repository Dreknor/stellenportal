<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #fff;
        }
        .success-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .details {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Guthaben erfolgreich aufgeladen</h1>
        </div>

        <div class="content">
            <p>Hallo {{ $user->first_name }} {{ $user->last_name }},</p>

            <p>Ihre Guthaben-Aufladung wurde erfolgreich durchgeführt!</p>

            <div class="success-box">
                <h2 style="margin: 0;">{{ number_format($package ? $package->credits : $transaction->credits, 0, ',', '.') }} Guthaben</h2>
                <p style="margin: 5px 0 0 0;">wurden erfolgreich gebucht</p>
            </div>

            <h3>Bestelldetails</h3>
            <div class="details">
                <table>
                    <tr>
                        <td>Transaktions-ID:</td>
                        <td>{{ $transaction->id }}</td>
                    </tr>
                    <tr>
                        <td>Datum:</td>
                        <td>{{ $transaction->created_at->format('d.m.Y H:i') }} Uhr</td>
                    </tr>
                    @if($package)
                    <tr>
                        <td>Paket:</td>
                        <td>{{ $package->name }}</td>
                    </tr>
                    <tr>
                        <td>Anzahl Guthaben:</td>
                        <td>{{ number_format($package->credits, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Preis:</td>
                        <td><strong>{{ number_format($package->price, 2, ',', '.') }} €</strong></td>
                    </tr>
                    @else
                    <tr>
                        <td>Paket:</td>
                        <td><em>Paketinformationen nicht verfügbar</em></td>
                    </tr>
                    <tr>
                        <td>Anzahl Guthaben:</td>
                        <td>{{ number_format($transaction->credits, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Preis:</td>
                        <td><strong>{{ number_format($transaction->amount, 2, ',', '.') }} €</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td>Gebucht für:</td>
                        <td>{{ $creditable->name }}</td>
                    </tr>
                    <tr>
                        <td>Neuer Kontostand:</td>
                        <td><strong>{{ number_format($transaction->balance_after, 0, ',', '.') }} Guthaben</strong></td>
                    </tr>
                    @if($transaction->note)
                    <tr>
                        <td>Notiz:</td>
                        <td>{{ $transaction->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <p>
                Sie erhalten in Kürze eine Rechnung über den gebuchten Betrag per separater E-Mail.
            </p>

            <p>
                Bei Fragen zu Ihrer Bestellung können Sie uns jederzeit kontaktieren.
            </p>

            <div class="footer">
                <p>
                    Dies ist eine automatisch generierte E-Mail. Bitte antworten Sie nicht direkt auf diese Nachricht.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
