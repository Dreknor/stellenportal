<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ __('Hilfe & FAQ') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Alles, was Sie über das Stellenportal wissen müssen') }}</p>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <a href="#overview" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-3">
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                    <x-icon name="fas-circle-info" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Überblick') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('Grundlegende Informationen über das Portal') }}</p>
        </a>

        <a href="#getting-started" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-3">
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                    <x-icon name="fas-rocket" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Erste Schritte') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('So starten Sie mit dem Portal') }}</p>
        </a>

        <a href="#organizations" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-3">
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                    <x-icon name="fas-building" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Trägerverwaltung') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('Träger anlegen und verwalten') }}</p>
        </a>

        <a href="#facilities" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-3">
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                    <x-icon name="fas-school" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Einrichtungsverwaltung') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('Einrichtungen erstellen und bearbeiten') }}</p>
        </a>

        <a href="#job-postings" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-3">
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                    <x-icon name="fas-briefcase" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Stellenausschreibungen') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('Stellenanzeigen erstellen und verwalten') }}</p>
        </a>

        <a href="#faq" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-3">
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                    <x-icon name="fas-circle-question" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('FAQ') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('Häufig gestellte Fragen') }}</p>
        </a>

        @if(config('mail.support_email'))
        <a href="#contact" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-3">
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-lg">
                    <x-icon name="fas-envelope" class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Kontakt') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('Direkt Kontakt aufnehmen') }}</p>
        </a>
        @endif
    </div>

    <!-- Überblick Section -->
    <div id="overview" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <x-icon name="fas-circle-info" class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-400" />
            {{ __('Überblick über das Stellenportal') }}
        </h2>

        <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Willkommen beim Stellenportal! Dieses System wurde entwickelt, um die Verwaltung und Veröffentlichung von Stellenanzeigen für Träger und Einrichtungen zu vereinfachen.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Hauptfunktionen') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center mb-2">
                        <x-icon name="fas-building" class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" />
                        {{ __('Trägerverwaltung') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Verwalten Sie Organisationen (Träger) und deren Grunddaten, Kontaktinformationen und Einrichtungen.') }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center mb-2">
                        <x-icon name="fas-school" class="h-5 w-5 mr-2 text-green-600 dark:text-green-400" />
                        {{ __('Einrichtungsverwaltung') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Erstellen und verwalten Sie Einrichtungen, die zu Ihren Trägern gehören, inklusive Adressen und Kontaktdaten.') }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center mb-2">
                        <x-icon name="fas-briefcase" class="h-5 w-5 mr-2 text-purple-600 dark:text-purple-400" />
                        {{ __('Stellenausschreibungen') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Erstellen, veröffentlichen und verwalten Sie Stellenanzeigen für Ihre Einrichtungen.') }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center mb-2">
                        <x-icon name="fas-coins" class="h-5 w-5 mr-2 text-yellow-600 dark:text-yellow-400" />
                        {{ __('Guthaben-System') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Verwalten Sie Credits für die Veröffentlichung von Stellenanzeigen auf Träger- und Einrichtungsebene.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Erste Schritte Section -->
    <div id="getting-started" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <x-icon name="fas-rocket" class="h-6 w-6 mr-2 text-green-600 dark:text-green-400" />
            {{ __('Erste Schritte') }}
        </h2>

        <div class="space-y-6">
            <!-- Schritt 1 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold">
                        1
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Träger anlegen') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Beginnen Sie mit dem Anlegen eines Trägers (Organisation). Gehen Sie dazu im Menü auf "Träger" und klicken Sie auf "Neuer Träger".') }}
                    </p>
                </div>
            </div>

            <!-- Schritt 2 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold">
                        2
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Einrichtungen hinzufügen') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Fügen Sie Einrichtungen zu Ihrem Träger hinzu. Diese können später als Arbeitgeber für Stellenanzeigen verwendet werden.') }}
                    </p>
                </div>
            </div>

            <!-- Schritt 3 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold">
                        3
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Guthaben aufladen') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Kaufen Sie Guthaben-Pakete für Ihren Träger oder Ihre Einrichtungen, um Stellenanzeigen veröffentlichen zu können.') }}
                    </p>
                </div>
            </div>

            <!-- Schritt 4 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold">
                        4
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Stellenanzeige erstellen') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Erstellen Sie Ihre erste Stellenanzeige über "Stellenausschreibungen" → "Neue Stellenausschreibung". Füllen Sie alle relevanten Informationen aus und speichern Sie zunächst als Entwurf.') }}
                    </p>
                </div>
            </div>

            <!-- Schritt 5 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold">
                        5
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Veröffentlichen') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Prüfen Sie Ihre Stellenanzeige und veröffentlichen Sie diese. Pro Veröffentlichung wird 1 Credit vom Guthaben abgezogen.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Organisationen Section -->
    <div id="organizations" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <x-icon name="fas-building" class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-400" />
            {{ __('Trägerverwaltung') }}
        </h2>

        <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Ein Träger ist die oberste Organisationsebene. Hier verwalten Sie grundlegende Informationen Ihrer Organisation.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Träger anlegen') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Um einen neuen Träger anzulegen, gehen Sie zu "Träger" im Hauptmenü und füllen Sie das Formular aus. Nach der Erstellung muss der Träger vom Administrator genehmigt werden, bevor Sie Credits kaufen und Einrichtungen erstellen können.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Träger bearbeiten') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('In der Träger-Übersicht können Sie Ihre Trägerdaten wie Name, Kontaktinformationen, Logo und Header-Bild bearbeiten. Diese Informationen werden auch auf den öffentlichen Seiten angezeigt.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Benutzer verwalten') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Sie können weitere Benutzer zu Ihrem Träger hinzufügen. Diese erhalten dann Zugriff auf alle Einrichtungen des Trägers. Die neuen Benutzer erhalten eine E-Mail mit ihren Zugangsdaten.') }}
            </p>
        </div>
    </div>

    <!-- Einrichtungen Section -->
    <div id="facilities" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <x-icon name="fas-school" class="h-6 w-6 mr-2 text-green-600 dark:text-green-400" />
            {{ __('Einrichtungsverwaltung') }}
        </h2>

        <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Einrichtungen gehören zu einem Träger und sind die konkreten Standorte, für die Sie Stellenanzeigen veröffentlichen.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Einrichtung anlegen') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Klicken Sie auf "Einrichtungen" → "Neue Einrichtung". Wählen Sie den zugehörigen Träger aus und geben Sie Name, Adresse und Kontaktdaten ein. Sie können auch ein Logo und Header-Bild hochladen.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Einrichtung bearbeiten') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('In der Einrichtungs-Detailansicht können Sie alle Daten Ihrer Einrichtung bearbeiten, einschließlich Bilder und Beschreibung.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Guthaben verwalten') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Jede Einrichtung kann ein eigenes Guthaben haben. Sie können Credits direkt für die Einrichtung kaufen oder vom Träger-Guthaben übertragen.') }}
            </p>
        </div>
    </div>

    <!-- Guthabenverwaltung Section -->
    <div id="credits" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <x-icon name="fas-coins" class="h-6 w-6 mr-2 text-yellow-600 dark:text-yellow-400" />
            {{ __('Guthabenverwaltung') }}
        </h2>

        <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Das Guthaben-System ermöglicht es Ihnen, Credits zu kaufen und zu verwalten, die für die Veröffentlichung von Stellenanzeigen benötigt werden. Jede Veröffentlichung kostet 1 Credit.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Zwei Ebenen der Guthabenverwaltung') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center mb-2">
                        <x-icon name="fas-building" class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" />
                        {{ __('Träger-Guthaben') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Credits auf Träger-Ebene können an einzelne Einrichtungen übertragen werden. Ideal für zentrale Verwaltung und flexible Verteilung.') }}
                    </p>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center mb-2">
                        <x-icon name="fas-school" class="h-5 w-5 mr-2 text-green-600 dark:text-green-400" />
                        {{ __('Einrichtungs-Guthaben') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Jede Einrichtung kann ein eigenes Guthaben haben. Diese Credits werden direkt für Stellenanzeigen dieser Einrichtung verwendet.') }}
                    </p>
                </div>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Guthaben kaufen') }}</h3>

            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Für Träger:') }}</h4>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                    <li>{{ __('Öffnen Sie die Detailansicht Ihres Trägers') }}</li>
                    <li>{{ __('Wechseln Sie zum Reiter "Guthaben"') }}</li>
                    <li>{{ __('Klicken Sie auf "Guthaben aufladen"') }}</li>
                    <li>{{ __('Wählen Sie ein Guthaben-Paket aus und schließen Sie den Kauf ab') }}</li>
                </ol>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Für Einrichtungen:') }}</h4>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                    <li>{{ __('Öffnen Sie die Detailansicht Ihrer Einrichtung') }}</li>
                    <li>{{ __('Wechseln Sie zum Reiter "Guthaben"') }}</li>
                    <li>{{ __('Klicken Sie auf "Guthaben aufladen"') }}</li>
                    <li>{{ __('Wählen Sie ein Guthaben-Paket aus und schließen Sie den Kauf ab') }}</li>
                </ol>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Credits vom Träger an Einrichtungen übertragen') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Als Träger können Sie Credits zentral kaufen und dann flexibel an Ihre Einrichtungen verteilen:') }}
            </p>

            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800 mb-4">
                <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                    <li>{{ __('Gehen Sie zur Träger-Detailansicht → Reiter "Guthaben"') }}</li>
                    <li>{{ __('Klicken Sie auf "An Einrichtung übertragen"') }}</li>
                    <li>{{ __('Wählen Sie die Ziel-Einrichtung aus') }}</li>
                    <li>{{ __('Geben Sie die Anzahl der Credits ein, die Sie übertragen möchten') }}</li>
                    <li>{{ __('Optional: Fügen Sie eine Notiz hinzu (z.B. "Budget Q1 2025")') }}</li>
                    <li>{{ __('Bestätigen Sie die Übertragung') }}</li>
                </ol>
                <p class="mt-3 text-sm text-blue-800 dark:text-blue-200 flex items-start">
                    <x-icon name="fas-info-circle" class="h-4 w-4 mr-2 mt-0.5 flex-shrink-0" />
                    <span>{{ __('Die Übertragung erfolgt sofort und wird in der Transaktionshistorie beider Seiten aufgezeichnet.') }}</span>
                </p>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Credits von Einrichtung an Träger zurückübertragen') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Nicht benötigte Credits können von einer Einrichtung zurück an den Träger übertragen werden. Dies kann von jedem Mitglied der Einrichtung ohne zusätzliche Berechtigungen durchgeführt werden:') }}
            </p>

            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800 mb-4">
                <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                    <li>{{ __('Gehen Sie zur Einrichtungs-Detailansicht → Reiter "Guthaben"') }}</li>
                    <li>{{ __('Klicken Sie auf "An Träger übertragen"') }}</li>
                    <li>{{ __('Geben Sie die Anzahl der Credits ein, die Sie zurückübertragen möchten') }}</li>
                    <li>{{ __('Optional: Fügen Sie einen Grund an (z.B. "Projekt abgeschlossen")') }}</li>
                    <li>{{ __('Bestätigen Sie die Übertragung') }}</li>
                </ol>
                <p class="mt-3 text-sm text-green-800 dark:text-green-200 flex items-start">
                    <x-icon name="fas-check-circle" class="h-4 w-4 mr-2 mt-0.5 flex-shrink-0" />
                    <span>{{ __('Keine zusätzlichen Berechtigungen erforderlich – jedes Mitglied der Einrichtung kann Credits übertragen.') }}</span>
                </p>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Transaktionshistorie') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Alle Guthaben-Bewegungen werden in einer übersichtlichen Historie aufgezeichnet. Sie finden die Transaktionshistorie im Guthaben-Tab jeder Organisation und Einrichtung.') }}
            </p>

            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Die Historie zeigt:') }}</h4>
                <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                    <li><strong>{{ __('Käufe:') }}</strong> {{ __('Welche Guthaben-Pakete wurden gekauft') }}</li>
                    <li><strong>{{ __('Übertragungen:') }}</strong> {{ __('Credits, die zwischen Träger und Einrichtungen übertragen wurden') }}</li>
                    <li><strong>{{ __('Verwendungen:') }}</strong> {{ __('Credits, die für Stellenanzeigen verwendet wurden') }}</li>
                    <li><strong>{{ __('Anpassungen:') }}</strong> {{ __('Manuelle Korrekturen durch Administratoren') }}</li>
                    <li><strong>{{ __('Ablauf:') }}</strong> {{ __('Credits, die nach 3 Jahren verfallen sind') }}</li>
                </ul>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Credit-Ablauf') }}</h3>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800 mb-4">
                <p class="text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Gekaufte Credits sind 3 Jahre ab Kaufdatum gültig. Sie erhalten Benachrichtigungen:') }}
                </p>
                <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300 ml-4">
                    <li>{{ __('90 Tage vor Ablauf') }}</li>
                    <li>{{ __('30 Tage vor Ablauf') }}</li>
                    <li>{{ __('7 Tage vor Ablauf') }}</li>
                </ul>
                <p class="mt-3 text-sm text-yellow-800 dark:text-yellow-200 flex items-start">
                    <x-icon name="fas-exclamation-triangle" class="h-4 w-4 mr-2 mt-0.5 flex-shrink-0" />
                    <span>{{ __('Abgelaufene Credits werden automatisch entfernt und können nicht mehr verwendet werden.') }}</span>
                </p>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Häufige Szenarien') }}</h3>

            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full h-6 w-6 flex items-center justify-center mr-2 text-sm">1</span>
                        {{ __('Zentrale Verwaltung') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Kaufen Sie Credits auf Träger-Ebene und verteilen Sie diese je nach Bedarf an Ihre Einrichtungen. So behalten Sie die zentrale Kontrolle über das Budget.') }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full h-6 w-6 flex items-center justify-center mr-2 text-sm">2</span>
                        {{ __('Dezentrale Verwaltung') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Jede Einrichtung kauft und verwaltet ihre eigenen Credits unabhängig. Ideal für autonome Einrichtungen mit eigenem Budget.') }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full h-6 w-6 flex items-center justify-center mr-2 text-sm">3</span>
                        {{ __('Hybrid-Modell') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Kombination aus beiden: Der Träger kauft ein Basis-Kontingent, Einrichtungen können bei Bedarf zusätzliche Credits selbst kaufen.') }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full h-6 w-6 flex items-center justify-center mr-2 text-sm">4</span>
                        {{ __('Rückübertragung bei Projektende') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Eine Einrichtung hat Credits für ein befristetes Projekt erhalten. Nach Abschluss werden ungenutzte Credits zurück an den Träger übertragen.') }}
                    </p>
                </div>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Berechtigungen') }}</h3>
            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                    <li class="flex items-start">
                        <x-icon name="fas-check" class="h-4 w-4 mr-2 mt-1 text-green-600 dark:text-green-400 flex-shrink-0" />
                        <span>{{ __('Credits kaufen: Jedes Mitglied eines Trägers oder einer Einrichtung') }}</span>
                    </li>
                    <li class="flex items-start">
                        <x-icon name="fas-check" class="h-4 w-4 mr-2 mt-1 text-green-600 dark:text-green-400 flex-shrink-0" />
                        <span>{{ __('Credits vom Träger an Einrichtung übertragen: Jedes Mitglied des Trägers') }}</span>
                    </li>
                    <li class="flex items-start">
                        <x-icon name="fas-check" class="h-4 w-4 mr-2 mt-1 text-green-600 dark:text-green-400 flex-shrink-0" />
                        <span>{{ __('Credits von Einrichtung an Träger übertragen: Jedes Mitglied der Einrichtung (keine zusätzlichen Rechte erforderlich)') }}</span>
                    </li>
                    <li class="flex items-start">
                        <x-icon name="fas-check" class="h-4 w-4 mr-2 mt-1 text-green-600 dark:text-green-400 flex-shrink-0" />
                        <span>{{ __('Transaktionshistorie ansehen: Jedes Mitglied des Trägers oder der Einrichtung') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stellenausschreibungen Section -->
    <div id="job-postings" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <x-icon name="fas-briefcase" class="h-6 w-6 mr-2 text-purple-600 dark:text-purple-400" />
            {{ __('Stellenausschreibungen') }}
        </h2>

        <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Stellenausschreibungen sind die Hauptfunktion des Portals. Hier erstellen und verwalten Sie Ihre Stellenanzeigen.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Stellenanzeige erstellen') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Gehen Sie zu "Stellenausschreibungen" → "Neue Stellenausschreibung". Wählen Sie die Einrichtung aus und füllen Sie alle Felder aus (Titel, Beschreibung, Anforderungen, etc.). Sie können die Anzeige zunächst als Entwurf speichern.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Stellenanzeige veröffentlichen') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Um eine Stellenanzeige zu veröffentlichen, benötigen Sie mindestens 1 Credit. Nach dem Veröffentlichen wird die Anzeige sofort auf der öffentlichen Seite sichtbar.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Stellenanzeige bearbeiten') }}</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ __('Sie können veröffentlichte Stellenanzeigen jederzeit bearbeiten. Die Änderungen werden sofort übernommen.') }}
            </p>

            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Status-Verwaltung') }}</h3>
            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                    <li><strong>{{ __('Entwurf:') }}</strong> {{ __('Die Anzeige ist nicht öffentlich sichtbar') }}</li>
                    <li><strong>{{ __('Aktiv:') }}</strong> {{ __('Die Anzeige ist veröffentlicht und öffentlich sichtbar') }}</li>
                    <li><strong>{{ __('Pausiert:') }}</strong> {{ __('Die Anzeige ist vorübergehend nicht sichtbar') }}</li>
                    <li><strong>{{ __('Abgelaufen:') }}</strong> {{ __('Die Laufzeit der Anzeige ist abgelaufen') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div id="faq" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center">
            <x-icon name="fas-circle-question" class="h-6 w-6 mr-2 text-purple-600 dark:text-purple-400" />
            {{ __('Häufig gestellte Fragen (FAQ)') }}
        </h2>

        <div class="space-y-4" x-data="{ openFaq: null }">
            <!-- FAQ Item 1 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 1 ? null : 1"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Wie erstelle ich eine Stellenanzeige?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 1 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Gehen Sie zu "Stellenausschreibungen" → "Neue Stellenausschreibung". Wählen Sie die Einrichtung aus, für die Sie die Stelle ausschreiben möchten, und füllen Sie alle erforderlichen Felder aus. Sie können die Anzeige zunächst als Entwurf speichern und später veröffentlichen.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 2 ? null : 2"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Wie funktioniert das Guthaben-System?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 2 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Das Guthaben-System basiert auf Credits. Jede Veröffentlichung einer Stellenanzeige kostet 1 Credit. Sie können Credits auf zwei Ebenen verwalten:') }}
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                        <li>{{ __('Träger-Ebene: Credits können an Einrichtungen weitergegeben werden') }}</li>
                        <li>{{ __('Einrichtungs-Ebene: Credits werden direkt für diese Einrichtung verwendet') }}</li>
                    </ul>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 3 ? null : 3"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Wie lange bleibt eine Stellenanzeige online?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 3 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Eine Stellenanzeige bleibt standardmäßig für die von Ihnen festgelegte Dauer online. Sie können die Laufzeit bei der Erstellung oder Veröffentlichung festlegen. Kurz vor Ablauf erhalten Sie eine E-Mail-Benachrichtigung. Sie können die Anzeige auch manuell pausieren, fortsetzen oder verlängern.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 4 ? null : 4"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Kann ich eine Stellenanzeige bearbeiten, nachdem sie veröffentlicht wurde?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 4 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Ja, Sie können veröffentlichte Stellenanzeigen jederzeit bearbeiten. Gehen Sie zur Übersicht der Stellenausschreibungen, wählen Sie die gewünschte Anzeige aus und klicken Sie auf "Bearbeiten". Die Änderungen werden sofort übernommen.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 5 ? null : 5"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Wie kann ich weitere Benutzer zu meinem Träger oder meiner Einrichtung hinzufügen?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 5 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 5" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Sie können Benutzer auf zwei Wegen hinzufügen:') }}
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                        <li>{{ __('Über die Träger-Detailansicht → Reiter "Benutzer" → "Benutzer hinzufügen"') }}</li>
                        <li>{{ __('Über die Einrichtungs-Detailansicht → Reiter "Benutzer" → "Benutzer hinzufügen"') }}</li>
                    </ul>
                    <p class="text-gray-600 dark:text-gray-400 mt-3">
                        {{ __('Die hinzugefügten Benutzer erhalten eine E-Mail mit ihren Zugangsdaten.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 6 ? null : 6"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Wie kann ich Credits von meinem Träger an eine Einrichtung übertragen?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 6 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 6" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Gehen Sie zur Detailansicht Ihres Trägers und wählen Sie den Reiter "Guthaben". Dort finden Sie die Option "Credits übertragen". Wählen Sie die Ziel-Einrichtung und die Anzahl der Credits aus, die Sie übertragen möchten.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 7 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 7 ? null : 7"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Wo finde ich eine Übersicht aller meiner Stellenanzeigen?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 7 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 7" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Im Hauptmenü finden Sie den Punkt "Stellenausschreibungen". Dort werden alle Ihre Stellenanzeigen aufgelistet. Sie können nach Status (Entwurf, Aktiv, Pausiert, Abgelaufen) filtern und die Anzeigen durchsuchen.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 8 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 8 ? null : 8"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Kann ich eine Stellenanzeige pausieren?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 8 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 8" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Ja, Sie können aktive Stellenanzeigen jederzeit pausieren. In der Detailansicht der Stellenanzeige finden Sie die Schaltfläche "Pausieren". Die Anzeige wird dann nicht mehr öffentlich angezeigt. Sie können sie später über "Fortsetzen" wieder aktivieren.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 9 - Credit-Pakete -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 9 ? null : 9"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Welche Guthaben-Pakete gibt es?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 9 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 9" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Die verfügbaren Guthaben-Pakete werden vom Administrator konfiguriert. Wenn Sie auf "Guthaben aufladen" klicken, sehen Sie alle für Sie verfügbaren Pakete mit Preisen und Anzahl der Credits. Größere Pakete bieten oft einen besseren Preis pro Credit.') }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Einige Pakete können Kauflimits haben (z.B. "Testpaket - maximal 1x pro Organisation"). Diese Informationen werden direkt beim Paket angezeigt.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 10 - Credit-Ablauf -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 10 ? null : 10"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Verfallen meine Credits?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 10 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 10" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Ja, gekaufte Credits haben eine Gültigkeit von 3 Jahren ab Kaufdatum. Sie erhalten automatisch E-Mail-Benachrichtigungen:') }}
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400 ml-4 mb-3">
                        <li>{{ __('90 Tage vor Ablauf') }}</li>
                        <li>{{ __('30 Tage vor Ablauf') }}</li>
                        <li>{{ __('7 Tage vor Ablauf') }}</li>
                    </ul>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Abgelaufene Credits werden automatisch aus Ihrem Guthaben entfernt und in der Transaktionshistorie als "Verfallen" markiert.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 11 - Credit-Übertragung -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 11 ? null : 11"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Kann ich Credits zwischen Einrichtungen übertragen?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 11 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 11" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Eine direkte Übertragung zwischen Einrichtungen ist nicht möglich. Sie können jedoch:') }}
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                        <li>{{ __('Credits von Einrichtung A zurück an den Träger übertragen') }}</li>
                        <li>{{ __('Vom Träger die Credits an Einrichtung B übertragen') }}</li>
                    </ol>
                    <p class="text-gray-600 dark:text-gray-400 mt-3">
                        {{ __('Dieser zweistufige Prozess stellt sicher, dass der Träger die zentrale Kontrolle über die Credit-Verteilung behält.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 12 - Credit-Rückübertragung -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 12 ? null : 12"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Kann ich Credits von meiner Einrichtung zurück an den Träger übertragen?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 12 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 12" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Ja, das ist problemlos möglich! Jedes Mitglied einer Einrichtung kann Credits an den Träger zurückübertragen, ohne dass dafür zusätzliche Berechtigungen erforderlich sind.') }}
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                        <li>{{ __('Gehen Sie zur Einrichtungs-Detailansicht → Transaktionshistorie"') }}</li>
                        <li>{{ __('Klicken Sie auf "An Träger übertragen"') }}</li>
                        <li>{{ __('Geben Sie die Anzahl ein und bestätigen Sie') }}</li>
                    </ol>
                    <p class="text-gray-600 dark:text-gray-400 mt-3">
                        {{ __('Dies ist nützlich, wenn Credits nicht mehr benötigt werden oder ein Projekt abgeschlossen ist.') }}
                    </p>
                </div>
            </div>

            <!-- FAQ Item 13 - Transaktionshistorie -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 13 ? null : 13"
                        class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('Wo kann ich alle Guthaben-Transaktionen einsehen?') }}
                        </h3>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="{ 'rotate-180': openFaq === 13 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === 13" x-collapse class="px-6 py-4 bg-white dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('Die Transaktionshistorie finden Sie:') }}
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                        <li>{{ __('Für Träger: Träger-Detailansicht → Reiter "Guthaben" → "Transaktionshistorie ansehen"') }}</li>
                        <li>{{ __('Für Einrichtungen: Einrichtungs-Detailansicht → Reiter "Guthaben" → "Transaktionshistorie ansehen"') }}</li>
                    </ul>
                    <p class="text-gray-600 dark:text-gray-400 mt-3">
                        {{ __('Die Historie zeigt alle Käufe, Übertragungen, Verwendungen und sonstige Bewegungen mit Datum, Benutzer, Betrag und Notizen.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kontakt Section -->
    @if(config('mail.support_email'))
    <div id="contact" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <x-icon name="fas-envelope" class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-400" />
            {{ __('Kontakt & Support') }}
        </h2>

        <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ __('Haben Sie eine Frage, die hier nicht beantwortet wurde? Nutzen Sie das Kontaktformular, um uns direkt zu erreichen. Wir werden uns so schnell wie möglich bei Ihnen melden.') }}
        </p>

        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-200">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('help.contact') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Ihr Name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', auth()->user()->name ?? '') }}"
                           required
                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Ihre E-Mail-Adresse') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', auth()->user()->email ?? '') }}"
                           required
                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Betreff') }} <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="subject"
                       name="subject"
                       value="{{ old('subject') }}"
                       required
                       placeholder="{{ __('z.B. Frage zur Trägerverwaltung') }}"
                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Ihre Nachricht') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="message"
                          name="message"
                          rows="6"
                          required
                          placeholder="{{ __('Bitte beschreiben Sie Ihr Anliegen so detailliert wie möglich...') }}"
                          class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Maximal 5000 Zeichen') }}
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="text-red-500">*</span> {{ __('Pflichtfelder') }}
                </p>
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <x-icon name="fas-paper-plane" class="h-4 w-4 mr-2" />
                    {{ __('Nachricht senden') }}
                </button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <strong>{{ __('Alternativ können Sie uns auch direkt per E-Mail kontaktieren:') }}</strong>
            </p>
            <p class="mt-2">
                <a href="mailto:{{ config('mail.support_email') }}"
                   class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    {{ config('mail.support_email') }}
                </a>
            </p>
        </div>
    </div>
    @endif
</x-layouts.app>

