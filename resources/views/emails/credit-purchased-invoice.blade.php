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
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #fff;
        }
        .invoice-details {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .invoice-details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Neue Guthaben-Bestellung</h1>
            <p>Rechnung erforderlich</p>
        </div>

        <div class="content">
            <p>Eine neue Guthaben-Bestellung wurde getätigt und erfordert die Erstellung einer Rechnung.</p>

            <div class="highlight">
                <strong>Transaktions-ID:</strong> {{ $transaction->id }}<br>
                <strong>Datum:</strong> {{ $transaction->created_at->format('d.m.Y H:i') }} Uhr
            </div>

            <h2>Käufer-Informationen</h2>
            <div class="invoice-details">
                <table>
                    <tr>
                        <td>Name:</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    </tr>
                    <tr>
                        <td>E-Mail:</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>Käufer für:</td>
                        <td>{{ class_basename($creditable) }}: {{ $creditable->name }}</td>
                    </tr>
                </table>
            </div>

            <h2>Rechnungsdetails</h2>
            <div class="invoice-details">
                <table>
                    <tr>
                        <td>Paket:</td>
                        <td>{{ $package->name }}</td>
                    </tr>
                    <tr>
                        <td>Beschreibung:</td>
                        <td>{{ $package->description ?? 'Keine Beschreibung' }}</td>
                    </tr>
                    <tr>
                        <td>Anzahl Guthaben:</td>
                        <td>{{ number_format($package->credits, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Preis:</td>
                        <td><strong>{{ number_format($package->price, 2, ',', '.') }} €</strong></td>
                    </tr>
                    <tr>
                        <td>Preis pro Guthaben:</td>
                        <td>{{ number_format($package->pricePerCredit, 4, ',', '.') }} €</td>
                    </tr>
                    @if($transaction->note)
                    <tr>
                        <td>Notiz:</td>
                        <td>{{ $transaction->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <h2>Rechnungsadresse</h2>
            <div class="invoice-details">
                <table>
                    <tr>
                        <td>Name:</td>
                        <td>{{ $creditable->name }}</td>
                    </tr>
                    @if($creditable->address)
                    <tr>
                        <td>Straße:</td>
                        <td>{{ $creditable->address->street }} {{ $creditable->address->house_number }}</td>
                    </tr>
                    <tr>
                        <td>PLZ / Ort:</td>
                        <td>{{ $creditable->address->zip }} {{ $creditable->address->city }}</td>
                    </tr>
                    @endif
                    @if($creditable->email)
                    <tr>
                        <td>E-Mail:</td>
                        <td>{{ $creditable->email }}</td>
                    </tr>
                    @endif
                    @if($creditable->phone)
                    <tr>
                        <td>Telefon:</td>
                        <td>{{ $creditable->phone }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <p style="margin-top: 30px;">
                <strong>Bitte erstellen Sie zeitnah eine Rechnung für diese Bestellung.</strong>
            </p>
        </div>
    </div>
</body>
</html>

