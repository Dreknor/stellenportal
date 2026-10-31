# Sicherheits- und Code-Audit – Stellenportal (31.10.2026)

Read-only-Analyse des gesamten Codes (Routen, Controller, Policies, Services, Models, Views, Config, Abhängigkeiten).
Mit ✔ markierte Funde wurden zusätzlich manuell im Code gegengeprüft.

## Übersicht

| Schwere | Anzahl |
|---|---|
| Kritisch | 7 |
| Hoch | 14 |
| Mittel | 17 |
| Niedrig | ~20 |

**Status:** K1–K7 behoben (31.10.2026), inkl. Regressionstests in `tests/Feature/SecurityFixesTest.php`, `tests/Unit/CreditExpirationTest.php`, `tests/Feature/DeleteUnverifiedUsersTest.php`. Für die Users-Resource wurde dabei auch der Middleware-Fehler aus „Niedrig“ (Array-Keys) behoben; die übrigen Resources haben ihn noch. Neue Migrationen: `2026_10_31_120000_null_user_foreign_keys_on_delete`, `2026_10_31_130000_create_credit_lots_table` (mit Backfill).

---

## KRITISCH

### K1 ✔ Organisationsübernahme durch jeden registrierten Benutzer
`app/Http/Controllers/Organizations/OrganizationUserController.php` – `index`, `store`, `destroy` prüfen **nicht**, ob der Benutzer Mitglied der Organisation ist (Routen `routes/web.php:61-63` haben nur `auth`/`verified`).
- `POST /organizations/{fremde-org}/users` mit eigener E-Mail → eigener Account wird angehängt → voller Zugriff auf Guthaben, Einrichtungen, Stellen.
- `GET …/users` zeigt Namen/E-Mails jeder Organisation, `DELETE …/users/{u}` entfernt beliebige Mitglieder.
- **Fix:** `OrganizationPolicy::manageUsers` + `Gate::authorize` in allen drei Methoden; in `destroy` prüfen, ob `$user` Mitglied ist; letztes Mitglied nicht entfernbar.

### K2 ✔ Stored XSS über JSON-LD (normale Org-Benutzer → alle Besucher inkl. Admins)
`resources/views/public/job-postings/show.blade.php:127,221`, `index.blade.php:136`, `welcome.blade.php:66,67,545`:
`{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}` in `<script>` ohne `JSON_HEX_TAG`.
Stellentitel `</script><script>…</script>` läuft auf Startseite, `/jobs` und Detailseite.
- **Fix:** `@json($schema)` bzw. `JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT`, `JSON_UNESCAPED_SLASHES` entfernen.

### K3 ✔ Uploads ohne Validierung → HTML/Polyglot-Dateien unter eigener Domain
`Organizations/OrganizationController.php:72-81` (store, keine Validierung von `header_image`/`logo`), `:169` (update, `logo`), `Facilities/FacilityController.php:104-113, 191, 226`, `Admin/PageImageController.php:37` (`getClientOriginalExtension()`).
Medialibrary übernimmt den Originalnamen inkl. Endung; `/storage/…/x.html` wird als `text/html` ausgeliefert → XSS gegen Admins. Zusätzlich hat medialibrary 11.17.0 einen bekannten Upload-Bypass (fix ≥ 11.23).
- **Fix:** überall `nullable|image|mimes:jpeg,jpg,png,webp|max:5120`; Dateiname serverseitig `Str::uuid().'.'.$file->guessExtension()`; medialibrary updaten; im Webserver unter `/storage` nur Bildendungen ausliefern + `X-Content-Type-Options: nosniff`.

### K4 ✔ Einrichtung in fremde Organisation verschiebbar
`Facilities/FacilityController.php:180,195`: `update` übernimmt `organization_id` (nur `exists`). Damit: Genehmigungspflicht umgehen, Genossenschafts-Pakete/Befreiungen erschleichen, Guthaben in fremde Org schieben, eigene Org verliert Einrichtung.
- **Fix:** `organization_id` aus `update` entfernen (nur Admin) oder `Rule::in($user->organizations->pluck('id'))` + Genehmigung prüfen.

### K5 ✔ `users:delete-unverified` löscht ALLE unverifizierten Benutzer jede Nacht – inkl. Credit-Journal
`app/Console/Commands/DeleteUnverifiedUsers.php:30-55`: `--days` wird ignoriert. Wer um 01:59 registriert oder seine E-Mail ändert (setzt `email_verified_at = null`), ist um 02:00 gelöscht. `credit_transactions.user_id` ist `onDelete('cascade')` → Kauf-/Verbrauchsbuchungen (Rechnungsnachweise) verschwinden. FK `job_postings.created_by` (restrict) kann den ganzen Lauf abbrechen. 3 Tests in `DeleteUnverifiedUsersTest` schlagen deshalb fehl.
- **Fix:** `->where('created_at', '<', now()->subDays($days))`, Benutzer mit Org-/Einrichtungszuordnung ausnehmen, per Model löschen (`each->delete()`, Auditing); FKs `credit_transactions.user_id`, `job_postings.created_by` auf `nullOnDelete()`.

### K6 ✔ Credit-Verfall vernichtet neue Credits
`app/Services/CreditService.php:318-336` + `CreditTransaction::scopeExpiredPurchases` (`:57-63`): Eine Verfallsbuchung entsteht nur bei Betrag > 0; „erledigt“ wird aber nur an einer existierenden Verfallsbuchung erkannt. Kauf 2023 vollständig verbraucht → kein Verfallseintrag → neu gekaufte Credits verfallen in der nächsten Nacht. Kein FIFO; an Einrichtungen transferierte Credits verfallen nie.
- **Fix:** pro Kauf `remaining` führen, Verbrauch/Transfers FIFO verrechnen (Verfallsdatum mitnehmen), nur `remaining` verfallen lassen; Kauf immer als verarbeitet markieren (`expired_processed_at`).

### K7 Rechteausweitung in der Admin-Benutzerverwaltung ✔
`Admin/UserController.php:84-86` (store), `:154-155` (update): `assignRole`/`syncRoles` mit jeder Rolle, Recht `assign roles` wird nie geprüft. Wer nur `admin edit users` hat, macht sich zum Super Admin bzw. ändert E-Mail/Passwort von Super Admins (Kontoübernahme).
- **Fix:** `Gate::authorize('assign', Role::class)`; keine Rollen mit mehr Rechten als der Handelnde vergeben; Super-Admin-Konten nur durch Super Admins bearbeitbar.

---

## HOCH

| # | Fund | Ort | Fix |
|---|---|---|---|
| H1 ✔ | Flash-Meldungen unescaped (`{!! $message !!}`), enthalten Benutzer-/Org-Namen → XSS in Admin-Session (Impersonate, Credit-Gutschrift) | `components/alerts/alert.blade.php:83`, `Admin/ImpersonateController.php:38,63`, `Admin/CreditController.php:93-162` | `{{ }}` verwenden |
| H2 | JS-Injection in `onclick="confirm('… {{ $user->name }}')"` (Entities werden im Attribut dekodiert) | `admin/users/index.blade.php:144`, `admin/users/show.blade.php:19` | `Js::from(...)` |
| H3 | Impersonation-Schutz nur über Rollennamen; `PreventImpersonationActions` nie registriert, falsche Routennamen | `Admin/ImpersonateController.php:28`, `Middleware/PreventImpersonationActions.php` | Ziel ablehnen bei `admin *`-Rechten / mehr Rechten; Middleware registrieren; `session()->regenerate()` |
| H4 | Keycloak verknüpft Konten nur per E-Mail, `email_verified` & `sub` ungeprüft (abhängig von Realm-Konfig) | `Auth/KeycloakController.php:58-73` | über `keycloak_id` matchen, `email_verified` verlangen |
| H5 | Klartext-Initialpasswörter in Queue-Payloads, sichtbar/durchsuchbar im Failed-Jobs-Admin | `Mail/UserCreatedMail.php`, `Mail/AdminUserCreatedMail.php`, `Admin/FailedJobController.php` | Passwort-Setzen-Link statt Passwort; mind. `ShouldBeEncrypted` |
| H6 ✔ | Öffentlicher, ungedrosselter Geocoding-Proxy; API-Key landet bei Fehlern im Log | `routes/web.php:444`, `Api/LocationSuggestionsController.php`, `JobPostingService.php:223,236` | `throttle`, Caching, Timeout, Key maskieren |
| H7 | Genossenschafts-Pakete von Nicht-Mitgliedern kaufbar (nur in der Anzeige gefiltert) | `CreditController.php:64-73,133-142` | `CreditPackage::active()->availableFor($org)->findOrFail()` |
| H8 | Credit-Befreiung missbrauchbar: als „Ehrenamt“ veröffentlichen, dann `employment_type` auf Vollzeit ändern | `JobPostingController.php:197-210`, `JobPostingService.php:35` | `employment_type` nach Veröffentlichung sperren oder nachbelasten |
| H9 | Doppelbelastung bei parallelem Veröffentlichen/Verlängern (Statusprüfung ohne Lock) | `JobPostingService.php:27,65` | Posting in Transaktion mit `lockForUpdate()` neu laden |
| H10 | Entwürfe/pausierte Stellen öffentlich per (erratbarem) Slug lesbar, inkl. PDF | `PublicJobPostingController.php:148-162` | 404 für `draft`/`paused` außer für Berechtigte |
| H11 | Warnmails vor Credit-Verfall gehen nie raus (Rollen `organization_admin` existieren nicht, Arrays statt Models) | `Console/Commands/NotifyExpiringCredits.php:112,139-165` | Empfänger = Benutzer der Org/Einrichtung, Restbestand melden |
| H12 | Pausierte Stellen, die während der Pause ablaufen, hängen für immer fest | `JobPostingService.php:65,126,141` | `paused` in `markExpiredPostings` oder `extend` erlauben |
| H13 | Passwort-Hashes & `remember_token` im Audit-Log für Log-Leser sichtbar | `config/audit.php:75,87`, `Models/User.php` | `$auditExclude = ['password','remember_token']`, Altdaten bereinigen |
| H14 | Abhängigkeiten: `composer audit` 63 Advisories / 18 Pakete (framework 12.35.1 CRLF in `email`-Regel, symfony/http-foundation, dompdf SVG-File-Read, guzzle, commonmark, phpseclib); `npm audit`: tinymce 8.2.2 (3× XSS, auch Kopie in `public/js/tinymce`) | `composer.lock`, `package-lock.json` | `composer update` (framework ≥12.68, medialibrary ≥11.23, dompdf ≥3.1.6), tinymce aktualisieren |

---

## MITTEL

- **M1** CMS-Rolle `cms` kann beliebiges HTML/JS einbringen (`{!! $page->content !!}`, HTML-Blöcke, `<textarea>{!! $block->content !!}`-Breakout) – kein Purifier. `public/pages/show.blade.php:60`, `content-block.blade.php:162,261,308,314`, `block-edit-form.blade.php:256`. → HTMLPurifier beim Speichern, Textarea mit `{{ }}`.
- **M2** `javascript:`-URLs in Menüpunkten, Content-Block-Buttons und Org-`website` (store nur `string`). `Admin/MenuController.php:45,89`, `Organizations/OrganizationController.php:53`. → `url:http,https`.
- **M3** CSP wirkungslos: `script-src 'unsafe-inline' 'unsafe-eval' https:` (`Middleware/SecurityHeaders.php:63-75`). → Nonce-CSP (`Vite::useCspNonce()`), Hosts explizit.
- **M4** Einrichtungs-/Org-Benutzer können beliebige bestehende Accounts per E-Mail ohne Zustimmung anhängen; `FacilityUserController::destroy` prüft Zugehörigkeit nicht (`:62-67,122`). → Einladung mit Bestätigung.
- **M5** Kauflimit pro Organisation per Race überschreitbar (`CreditService.php:48`, Prüfung vor Transaktion). → Org-Zeile sperren, in Transaktion prüfen.
- **M6** Credits werden ohne Zahlung/Freigabe gutgeschrieben, jedes Mitglied kann unbegrenzt kaufen (`CreditService.php:52-75`) – Geschäftsentscheidung, ggf. Kaufrecht/Status „pending“.
- **M7** Rechnungsmail zeigt ggf. falschen Preis (Package wird beim Versand neu geladen), Fallback zeigt Credits als €, `$transaction->credits` existiert nicht; `address->house_number`/`zip` existieren nicht (`number`/`zip_code`).
- **M8** Inhalt aktiver Anzeigen beliebig austauschbar (1 Credit = beliebig viele Stellen nacheinander); Slug ändert sich bei Titeländerung (`JobPosting.php:78-83`, `doNotGenerateSlugsOnUpdate()` fehlt).
- **M9** Löschen einer Einrichtung/Organisation vernichtet Guthaben, hinterlässt verwaiste polymorphe Balances (`FacilityController.php:238-248`, `Admin/OrganizationController::destroy`).
- **M10** Admin-Transaktionsfilter nutzt nicht existierende Spalte `transactionable_type` → 500 (`Admin/CreditController.php:47-51`).
- **M11** Synchrones Geocoding + Kartendownload in jedem Address-Save (bis 5×10 s), `GenerateAddressMapJob` nie dispatcht; OSM-Tiles mit gefälschtem UA + Admin-URL als Referer (Verstoß Tile Policy). → Queue-Job `afterCommit`, eigener UA.
- **M12** `LogEntryObserver` feuert nie (`config/logtodb.php:62` `model => false`), `MAIL_SUPPORT_ADDRESS` leer → Critical-Log-Mails tot.
- **M13** Unbegrenzte Volltextsuche ohne Throttle (beliebig viele Terme × 5 `LIKE '%…%'`), `PublicJobPostingController.php:46-58`. Keine SQL-Injection.
- **M14** `RolePolicy.php:48`: Super-Admin-Schutz nur per Name (umbenennen → löschen); wer `edit roles` hat, gibt der eigenen Rolle alle Rechte.
- **M15** Account-Löschung ohne Passwortbestätigung (`Settings/ProfileController.php:50`).
- **M16** `logs/clear` nur mit `admin view logs` (`routes/web.php:304`) → Spuren verwischen.
- **M17** E-Mail-Änderung im Profil ohne Passwort; in Kombination mit K5 → Löschung über Nacht.

## NIEDRIG

- `JobPostingPolicy.php:151` `hasRole('admin')` – Rolle heißt `Admin` (toter Code).
- `Organization::$fillable` enthält `is_approved`, `approved_by`, `is_cooperative_member`; `Facility::$fillable` `organization_id` (Defense in Depth).
- `Route::resource(...)->middleware([...])` mit Array-Keys: Keys werden ignoriert → alle Permissions UND-verknüpft auf jeder Aktion (`routes/web.php:149-158`) → `middlewareFor()`.
- CMS-Routen ohne `PasswordExpired` (`routes/web.php:347`); `PasswordExpired` prüft Session-Key `ownID` statt `impersonate_original_user` und ist global vor der Session registriert.
- `credits/packages` registriert `show`, Controller hat keine → 500 (`->except('show')`).
- Dashboard zeigt allen Benutzern globale Zahlen.
- PageImage/ContentBlock ohne Seitenzugehörigkeitsprüfung (`scopeBindings()`); PageImage-Dateien werden nie gelöscht (falsche Disk), Duplikate teilen sich `path`.
- `{!! $initials !!}` in Cards (max. 2 Zeichen, nur Markup-Bruch).
- `FailedJobController.php:95` `unserialize()` statt `queue:retry`.
- Keycloak loggt Konfiguration bei jedem Redirect, Exception-Text in Flash-Meldung.
- reCAPTCHA beim Login auskommentiert, fehlt bei forgot-password; kein Throttle auf `confirm-password`.
- `Password::defaults()` nicht konfiguriert (nur min. 8 Zeichen) → `min(12)->uncompromised()`.
- `SESSION_SECURE_COOKIE` fehlt in `.env.example`.
- `firstOrCreate` für CreditBalance nicht race-sicher; Admin-Gutschrift `amount` ohne `max`.
- `Admin/JobPostingController.php:82-89`: `employment_type` ohne `in:`; `salary_min/max` validiert aber verworfen (Migration leer).
- `JobPostingCreditExemption` nicht `Auditable` (entgegen Konvention).
- Mails in `DB::transaction` ohne `afterCommit()`.
- `addMonths` statt `addMonthsNoOverflow` (30.11. + 3 M = 02.03.).
- Fehlende Pagination in Admin-Credit-/JobPosting-Listen; `canBePurchasedBy` 3 Queries pro Paket.
- `spatie/laravel-sluggable: "*"` → `^3.7`; `maennchen/zipstream-php: "3.1"` → `^3.1`.
- `cms_check_result.txt` im Repo getrackt.

---

## Qualität / Tooling

- **PHPStan (Level 7):** 576 Fehler, überwiegend fehlende Typen. Echte Treffer: 9× `method.notFound` (z. B. `Admin/CreditController:132,149`, `ContentBlockController:49`), 9× `argument.type`, 2× `arguments.count`.
- **Tests:** 103 fehlgeschlagen / 208 bestanden. Hauptursachen: Test-Setup (Rollen/Permissions doppelt angelegt bzw. fehlend, Unit-Tests ohne Laravel-`TestCase`), daneben echte Fehler (`DeleteUnverifiedUsersTest` → K5) und veraltete Erwartungen.

## Geprüft und unauffällig

CSRF ohne Ausnahmen; keine SQL-Injection (alles gebunden); keine Open Redirects; `JobPostingController`/`CreditController`/Rollen-Controller per Policy abgesichert; Credit-Mutationen mit `lockForUpdate` in Transaktionen; Paketpreis aus DB; Stellen-Rich-Text überall `nl2br(e())`; dompdf ohne Remote/PHP; keine Secrets im Repo; `public/` sauber; `APP_DEBUG` default false; Login-Throttle & Session-Regeneration vorhanden; Socialite-State geprüft.
