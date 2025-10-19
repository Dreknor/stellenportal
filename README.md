# Stellenportal (Laravel 12)

Ein modernes Job-Portal für Organisationen und Einrichtungen auf Basis von Laravel 12 und Vite. Es unterstützt ein Credit-System für Stellenausschreibungen, Rollen & Berechtigungen, einen Admin-Bereich, öffentliche Job-Ansichten inkl. PDF-Export und SEO-Sitemap.

## Features

- Öffentliche Jobs
  - Listen-/Detailseite unter `/jobs`
  - PDF-Export einzelner Stellenanzeigen
  - SEO-Sitemap unter `/sitemap.xml`
- Bewerter- und Management-Funktionen
  - Dashboard für eingeloggte, verifizierte Nutzer (`/dashboard`)
  - Profil-, Passwort- und Erscheinungs-Einstellungen
- Organisationen und Einrichtungen
  - CRUD für Organisationen und Einrichtungen
  - Nutzer-Management: Zuweisung/Entfernung von Nutzern zu Organisationen/Einrichtungen
- Job Postings (Stellenanzeigen)
  - CRUD mit Aktionen: Veröffentlichen, Pausieren, Fortsetzen, Verlängern
  - Mediathek-Integration, Slugs, Status- und Veröffentlichungslogik
- Credits (Guthaben)
  - Credit-Pakete verwalten (Admin mit entsprechender Berechtigung)
  - Credits für Organisationen/Einrichtungen kaufen, Transaktionen einsehen
  - Umbuchung von Organisation → Einrichtung
- Rollen & Berechtigungen
  - Feingranulare Rechte via Spatie Permission
  - Seeder weist dem User mit ID 1 alle Berechtigungen zu
- Auditing & Logging
  - Änderungsprotokolle (Audits) im Admin-Bereich
- Security & UX
  - reCAPTCHA-Integration (Google)
  - Passwort-Expired-Middleware
- Frontend
  - Tailwind CSS 4, Alpine.js, Vite 6

## Tech-Stack

- Backend: PHP >= 8.2, Laravel ^12.0
- Frontend: Vite ^6, Tailwind CSS ^4, Alpine.js ^3
- Datenbank: standardmäßig SQLite (konfigurierbar auf MySQL/MariaDB/PostgreSQL/SQL Server)
- Wichtige Pakete:
  - spatie/laravel-permission (Rollen & Berechtigungen)
  - spatie/laravel-medialibrary (Medienverwaltung)
  - owen-it/laravel-auditing (Audits)
  - barryvdh/laravel-dompdf (PDF-Export)
  - buzz/laravel-google-captcha (reCAPTCHA)
  - dantsu/php-osm-static-api (statische Karten, falls genutzt)

## Voraussetzungen

- Windows, macOS oder Linux
- PHP 8.2+, Composer
- Node.js 18+ und npm
- SQLite3 (oder alternativ MySQL/MariaDB/PG/SQL Server)

## Schnellstart (Windows, cmd)

1) Abhängigkeiten installieren

```bat
composer install
npm install
```

2) Environment anlegen und App-Key generieren

```bat
copy .env.example .env
php artisan key:generate
```

3) Datenbank vorbereiten

- Standard: SQLite ist vorkonfiguriert (`config/database.php`). In diesem Repo existiert `database/database.sqlite`. Falls nicht vorhanden:

```bat
type NUL > database\database.sqlite
```

- Migrationen ausführen (optional: mit Seedern):

```bat
php artisan migrate
php artisan db:seed
```

Hinweis: Der Seeder `AssignAllPermissionsToUserOneSeeder` vergibt alle Berechtigungen an den Benutzer mit ID 1. Existiert dieser Benutzer noch nicht, erscheint eine Meldung und die Zuweisung wird übersprungen (siehe Abschnitt „Admin-Rechte für ersten Benutzer“).

4) Entwicklung starten

- Alles in einem: Composer-Script startet PHP-Server, Queue-Listener und Vite parallel

```bat
composer run dev
```

- Oder manuell in separaten Terminals:

```bat
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

5) Öffnen

- App: http://127.0.0.1:8000
- Öffentliche Jobs: http://127.0.0.1:8000/jobs
- Dashboard (nach Login/Verifizierung): http://127.0.0.1:8000/dashboard
- Admin-Dashboard (mit Berechtigungen): http://127.0.0.1:8000/admin/dashboard

## Admin-Rechte für ersten Benutzer

Der Seeder `AssignAllPermissionsToUserOneSeeder` weist allen Berechtigungen dem Nutzer mit ID 1 zu. Empfohlener Ablauf:

1) Server starten und über „Registrieren“ einen ersten Benutzer erstellen (erhält i. d. R. ID 1).
2) Dann Berechtigungen zuweisen:

```bat
php artisan db:seed --class=Database\Seeders\AssignAllPermissionsToUserOneSeeder
```

Sollte der erste Benutzer bereits existieren, kann auch einfach erneut `php artisan db:seed` ausgeführt werden.

## Konfiguration (.env)

Wichtige Einstellungen:

- APP_URL, APP_ENV, APP_DEBUG
- Datenbank (optional, wenn nicht SQLite):
  - `DB_CONNECTION=mysql` | `pgsql` | `sqlsrv`
  - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Mail (für Verifizierung/Benachrichtigungen):
  - `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
- reCAPTCHA (siehe `config/captcha.php`):
  - `CAPTCHA_SECRET=your_secret`
  - `CAPTCHA_SITEKEY=your_sitekey`
- Queue (optional, empfohlen für Mails/Jobs):
  - `QUEUE_CONNECTION=database`
  - Jobs-Tabelle anlegen und Worker starten:

```bat
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## Entwicklungs-Workflow

- Code-Style: Laravel Pint kann optional genutzt werden (`composer require --dev laravel/pint` ist bereits enthalten). 
- Frontend: `npm run dev` für HMR; `npm run build` für Production Build.
- Datenbank: Migrationen/Seeder wie üblich via Artisan.

## Tests

- Test-Runner (Pest):

```bat
composer test
```

## Deployment (Kurzüberblick)

- Production Build der Assets:

```bat
npm run build
```

- Laravel vorbereiten (Beispiele):

```bat
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- APP_ENV/APP_DEBUG korrekt setzen, Queues/Crons einrichten, Dateirechte und Cache-Strategien gemäß Zielumgebung.

## Wichtige Routen (Auszug)

- `/` – Startseite mit den 5 neuesten aktiven Jobs
- `/jobs` – Öffentliche Jobliste; `/{jobPosting}` Detail; `/{jobPosting}/pdf` PDF-Export
- `/dashboard` – Benutzer-Dashboard (auth + verified)
- `/admin/...` – Admin-Bereich (Rechte erforderlich)

## Troubleshooting

- „Benutzer mit ID 1 wurde nicht gefunden!“ beim Seed:
  - Zuerst einen Benutzer registrieren, dann `AssignAllPermissionsToUserOneSeeder` erneut ausführen.
- Vite / HMR lädt nicht:
  - Sicherstellen, dass `npm run dev` läuft und die Seite mit aktivem Vite-Plugin ausgeliefert wird.
- SQLite-Probleme (Pfad/Lock):
  - Prüfen, ob `database/database.sqlite` existiert und Schreibrechte vorhanden sind. Editor/Tools schließen, die die Datei sperren.
- reCAPTCHA schlägt fehl:
  - `CAPTCHA_SECRET` und `CAPTCHA_SITEKEY` korrekt setzen; Domain im Google-Dashboard freischalten.

## Lizenz

MIT (siehe `composer.json`).

## Danksagung

Dieses Projekt basiert auf dem Laravel-Ökosystem und etablierten Open-Source-Paketen (u. a. von Spatie, LaravelDaily u. a.).

