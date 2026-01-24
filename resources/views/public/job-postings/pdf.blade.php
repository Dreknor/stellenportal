<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $jobPosting->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-section {
            margin-bottom: 20px;
        }
        .facility-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        .facility-initials {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #86efac 0%, #22c55e 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            color: #166534;
            border: 2px solid #22c55e;
        }
        h1 {
            font-size: 24pt;
            margin: 15px 0 10px 0;
            color: #1e293b;
        }
        h2 {
            font-size: 16pt;
            margin: 25px 0 15px 0;
            color: #334155;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }
        h3 {
            font-size: 14pt;
            margin: 20px 0 10px 0;
            color: #475569;
        }
        .meta-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .meta-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .meta-label {
            display: table-cell;
            font-weight: bold;
            color: #64748b;
            width: 40%;
        }
        .meta-value {
            display: table-cell;
            color: #1e293b;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: bold;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .badge-primary {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }
        .content-section {
            margin-bottom: 25px;
        }
        .facility-info {
            background-color: #f0fdf4;
            padding: 15px;
            border-radius: 8px;
            margin-top: 30px;
            border-left: 4px solid #22c55e;
        }
        .contact-info {
            background-color: #eff6ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #2563eb;
        }
        .contact-item {
            margin-bottom: 8px;
            padding-left: 20px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            font-size: 9pt;
            color: #64748b;
            text-align: center;
        }
        p {
            margin: 10px 0;
        }
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            @php
                // Compute initials for placeholder
                $initials = '';
                $nameParts = preg_split('/\s+/', trim((string) $jobPosting->facility->name));
                if (!empty($nameParts)) {
                    $firstTwo = array_slice($nameParts, 0, 2);
                    foreach ($firstTwo as $part) {
                        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                    }
                }
            @endphp

            @if($headerImage)
                <img src="{{ $headerImage }}" alt="{{ $jobPosting->facility->name }}" class="facility-logo">
            @else
                <div class="facility-initials">{{ $initials }}</div>
            @endif
        </div>

        <h1>{{ $jobPosting->title }}</h1>

        <div style="margin-top: 10px;">
            <span class="badge badge-primary">{{ $jobPosting->getEmploymentTypeLabel() }}</span>
            @if($jobPosting->job_category)
                <span class="badge badge-secondary">{{ $jobPosting->job_category }}</span>
            @endif
        </div>
    </div>

    <!-- Expired Notice -->
    @if(isset($isExpired) && $isExpired)
        <div style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
            <h3 style="color: #92400e; margin: 0 0 10px 0;">⚠️ Dieses Stellenangebot ist nicht mehr verfügbar</h3>
            <p style="color: #78350f; margin: 0;">Diese Stellenausschreibung wurde beendet oder ist abgelaufen.</p>
        </div>
    @endif

    <!-- Meta Information -->
    <div class="meta-info">
        <div class="meta-row">
            <span class="meta-label">Einrichtung:</span>
            <span class="meta-value">{{ $jobPosting->facility->name }}</span>
        </div>
        @if($jobPosting->facility->organization)
            <div class="meta-row">
                <span class="meta-label">Träger:</span>
                <span class="meta-value">{{ $jobPosting->facility->organization->name }}</span>
            </div>
        @endif
        @if($jobPosting->facility->address)
            <div class="meta-row">
                <span class="meta-label">Standort:</span>
                <span class="meta-value">
                    {{ $jobPosting->facility->address->street }} {{ $jobPosting->facility->address->number }},
                    {{ $jobPosting->facility->address->zip_code }} {{ $jobPosting->facility->address->city }}
                </span>
            </div>
        @endif
        <div class="meta-row">
            <span class="meta-label">Veröffentlicht am:</span>
            <span class="meta-value">{{ $jobPosting->published_at->format('d.m.Y') }}</span>
        </div>
        @if($jobPosting->expires_at)
            <div class="meta-row">
                <span class="meta-label">Bewerbung bis:</span>
                <span class="meta-value">{{ $jobPosting->expires_at->format('d.m.Y') }}</span>
            </div>
        @endif
    </div>

    <!-- Job Description -->
    <div class="content-section">
        <h2>Stellenbeschreibung</h2>
        <div>{!! nl2br(e($jobPosting->description)) !!}</div>
    </div>

    <!-- Requirements -->
    @if($jobPosting->requirements)
        <div class="content-section">
            <h2>Das bringen Sie mit</h2>
            <div>{!! nl2br(e($jobPosting->requirements)) !!}</div>
        </div>
    @endif

    <!-- Benefits -->
    @if($jobPosting->benefits)
        <div class="content-section">
            <h2>Das bieten wir Ihnen</h2>
            <div>{!! nl2br(e($jobPosting->benefits)) !!}</div>
        </div>
    @endif

    <!-- Facility Information -->
    <div class="facility-info">
        <h3 style="margin-top: 0;">Über die Einrichtung</h3>
        <p><strong>{{ $jobPosting->facility->name }}</strong></p>
        @if($jobPosting->facility->organization)
            <p>{{ $jobPosting->facility->organization->name }}</p>
        @endif
        @if($jobPosting->facility->description)
            <p style="margin-top: 10px;">{{ Str::limit($jobPosting->facility->description, 300) }}</p>
        @endif
    </div>

    <!-- Contact Information -->
    @if($jobPosting->contact_person || $jobPosting->contact_email || $jobPosting->contact_phone)
        <div class="contact-info">
            <h3 style="margin-top: 0;">Kontakt für Bewerbungen</h3>
            @if($jobPosting->contact_person)
                <div class="contact-item">
                    <strong>Ansprechpartner:</strong> {{ $jobPosting->contact_person }}
                </div>
            @endif
            @if($jobPosting->contact_email)
                <div class="contact-item">
                    <strong>E-Mail:</strong> {{ $jobPosting->contact_email }}
                </div>
            @endif
            @if($jobPosting->contact_phone)
                <div class="contact-item">
                    <strong>Telefon:</strong> {{ $jobPosting->contact_phone }}
                </div>
            @endif
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dieses Stellenangebot wurde am {{ now()->format('d.m.Y') }} erstellt.</p>
        <p>Für die Aktualität und Richtigkeit der Angaben übernehmen wir keine Gewähr.</p>
    </div>
</body>
</html>

