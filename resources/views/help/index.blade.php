<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ __('Hilfe & FAQ') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Alles, was Sie über das Stellenportal wissen müssen') }}</p>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
        </div>
    </div>

    <!-- Kontakt Section -->
    @if(config('mail.support_email'))
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg shadow-md p-6 border border-blue-200 dark:border-gray-600">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-3">
            {{ __('Weitere Fragen?') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            {{ __('Wenn Sie weitere Fragen haben oder Unterstützung benötigen, kontaktieren Sie uns gerne.') }}
        </p>
        <div class="flex flex-wrap gap-4">
            <a href="mailto:{{ config('mail.support_email') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <x-icon name="fas-envelope" class="h-4 w-4 mr-2" />
                {{ __('E-Mail Support') }}
            </a>
        </div>
    </div>
    @endif
</x-layouts.app>

