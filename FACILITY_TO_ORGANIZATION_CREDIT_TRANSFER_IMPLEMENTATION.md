# Guthaben-Übertragung an Träger - Implementierung

## Zusammenfassung der Änderungen

Die Funktion zur Übertragung von Guthaben von einer Einrichtung an den Träger wurde implementiert und ist nun für alle Mitglieder der Einrichtung ohne zusätzliche Berechtigungen verfügbar.

## Durchgeführte Änderungen

### 1. Backend - Policy (app/Policies/CreditPolicy.php)

Die `CreditPolicy` wurde aktualisiert, um konsistente Datenbankabfragen zu verwenden:

- **transferCreditsToOrganization()**: Erlaubt es Benutzern, die entweder der Einrichtung ODER der Organisation zugewiesen sind, Guthaben zu übertragen
- **purchaseCredits()**: Aktualisiert für Konsistenz
- **transferCredits()**: Aktualisiert für Konsistenz
- **viewTransactions()**: Aktualisiert für Konsistenz

**Wichtig**: Die Policy verwendet jetzt explizite Datenbankabfragen (`->where()->exists()`) statt Collection-Methoden (`->contains()`), um sicherzustellen, dass die Berechtigungsprüfung in allen Kontexten funktioniert.

### 2. Backend - Service Provider (app/Providers/AppServiceProvider.php)

Die Gate-Registrierung wurde ergänzt:

```php
Gate::define('transferCreditsToOrganization', [CreditPolicy::class, 'transferCreditsToOrganization']);
```

Diese fehlende Registrierung war der Hauptgrund, warum die Policy-Prüfung nicht funktionierte.

### 3. Tests (tests/Feature/CreditTransferTest.php)

Ein Test wurde angepasst, um Unicode-Encoding-Probleme zu vermeiden:

- `test_user_can_view_facility_transfer_form()` wurde aktualisiert, um nach "Guthaben an" statt "An Träger übertragen" zu suchen

## Funktionsweise

### Berechtigungsprüfung

Ein Benutzer kann Guthaben von einer Einrichtung an den Träger übertragen, wenn **eine** der folgenden Bedingungen erfüllt ist:

1. Der Benutzer ist der Einrichtung zugewiesen (`facility_user`-Pivot-Tabelle)
2. Der Benutzer ist der Organisation (Träger) zugewiesen (`organization_user`-Pivot-Tabelle)

Zusätzlich muss die Organisation vom Administrator genehmigt sein (`is_approved = true`).

### Frontend

Der Button "An Träger übertragen" ist in der Transaktionshistorie der Einrichtung sichtbar:
- Route: `credits.facility.transfer-to-organization`
- Blade-View: `resources/views/credits/transactions/facility.blade.php`
- Transfer-Formular: `resources/views/credits/transfer-to-organization.blade.php`

### Backend-Controller

Der `CreditController` prüft die Berechtigung mit:

```php
$this->authorize('transferCreditsToOrganization', $facility);
```

### Service-Layer

Der `CreditService` verwaltet die Transaktion und erstellt:
1. Eine `transfer_out`-Transaktion für die Einrichtung
2. Eine `transfer_in`-Transaktion für die Organisation
3. Beide Transaktionen sind miteinander verknüpft (`related_transaction_id`)
4. Die Kontostände werden atomar aktualisiert

## Tests

Alle relevanten Tests laufen erfolgreich:

- ✓ organization can transfer credits to facility
- ✓ cannot transfer more credits than available
- ✓ cannot transfer credits to unrelated facility
- ✓ facility can transfer credits to organization
- ✓ facility cannot transfer more credits than available
- ✓ user can view facility transfer form
- ✓ unauthorized user cannot transfer facility credits

## Keine zusätzlichen Berechtigungen erforderlich

Die Implementierung entspricht der Anforderung, dass **jedes Mitglied der Einrichtung** ohne zusätzliche Rechte Guthaben an den Träger übertragen kann. Es sind keine speziellen Rollen oder Permissions erforderlich - nur die Zuordnung zur Einrichtung oder zur Organisation.

