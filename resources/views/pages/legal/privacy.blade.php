<x-layouts.public>
    <x-slot name="title">{{ __('Datenschutzerklärung') }} - LearningPilot</x-slot>

    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-3xl px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Datenschutzerklärung') }}</h1>
            <p class="mt-4 text-sm text-gray-500">{{ __('Stand: Januar 2025') }}</p>

            <div class="mt-10 prose prose-indigo max-w-none">
                <h2>1. {{ __('Verantwortlicher') }}</h2>
                <p>
                    LearningPilot<br>
                    Musterstrasse 123<br>
                    8000 Zürich<br>
                    Schweiz<br>
                    E-Mail: hello@learningpilot.ch
                </p>

                <h2>2. {{ __('Erhebung und Verarbeitung personenbezogener Daten') }}</h2>
                <p>{{ __('Wir erheben und verarbeiten personenbezogene Daten nur, soweit dies zur Bereitstellung unserer Dienste erforderlich ist oder Sie uns Ihre Einwilligung erteilt haben.') }}</p>

                <h3>2.1 {{ __('Bei Registrierung') }}</h3>
                <p>{{ __('Bei der Registrierung für unseren Dienst erheben wir folgende Daten:') }}</p>
                <ul>
                    <li>{{ __('Name') }}</li>
                    <li>{{ __('E-Mail-Adresse') }}</li>
                    <li>{{ __('Passwort (verschlüsselt)') }}</li>
                    <li>{{ __('Optional: Organisation/Schule') }}</li>
                </ul>

                <h3>2.2 {{ __('Bei Nutzung der Plattform') }}</h3>
                <p>{{ __('Während der Nutzung unserer Lernplattform erfassen wir:') }}</p>
                <ul>
                    <li>{{ __('Lernfortschritt und abgeschlossene Module') }}</li>
                    <li>{{ __('Testergebnisse und Bewertungen') }}</li>
                    <li>{{ __('Zeitaufwand für Lernaktivitäten') }}</li>
                    <li>{{ __('Hochgeladene Dateien und Abgaben') }}</li>
                </ul>

                <h2>3. {{ __('Zweck der Datenverarbeitung') }}</h2>
                <p>{{ __('Wir verarbeiten Ihre Daten für folgende Zwecke:') }}</p>
                <ul>
                    <li>{{ __('Bereitstellung und Verbesserung unserer Lernplattform') }}</li>
                    <li>{{ __('Verfolgung Ihres Lernfortschritts') }}</li>
                    <li>{{ __('Ausstellung von Zertifikaten') }}</li>
                    <li>{{ __('Kommunikation bezüglich Ihres Kontos') }}</li>
                    <li>{{ __('Rechnungsstellung und Zahlungsabwicklung') }}</li>
                </ul>

                <h2>4. {{ __('KI-gestützte Funktionen') }}</h2>
                <p>{{ __('Unsere Plattform bietet KI-gestützte Funktionen wie einen KI-Tutor und automatisch generierte Übungen. Bei der Nutzung dieser Funktionen:') }}</p>
                <ul>
                    <li>{{ __('Werden Ihre Anfragen an unseren KI-Anbieter (Anthropic) übermittelt') }}</li>
                    <li>{{ __('Wird der Kontext Ihres Lernfortschritts genutzt, um relevante Antworten zu generieren') }}</li>
                    <li>{{ __('Werden keine Daten für das Training der KI verwendet') }}</li>
                </ul>

                <h2>5. {{ __('Datenweitergabe') }}</h2>
                <p>{{ __('Wir geben Ihre personenbezogenen Daten nur in folgenden Fällen an Dritte weiter:') }}</p>
                <ul>
                    <li>{{ __('An Zahlungsdienstleister (Stripe) zur Abwicklung von Zahlungen') }}</li>
                    <li>{{ __('An KI-Anbieter (Anthropic) zur Bereitstellung von KI-Funktionen') }}</li>
                    <li>{{ __('Wenn wir gesetzlich dazu verpflichtet sind') }}</li>
                </ul>

                <h2>6. {{ __('Datensicherheit') }}</h2>
                <p>{{ __('Wir setzen technische und organisatorische Sicherheitsmassnahmen ein, um Ihre Daten zu schützen:') }}</p>
                <ul>
                    <li>{{ __('SSL/TLS-Verschlüsselung für alle Datenübertragungen') }}</li>
                    <li>{{ __('Verschlüsselte Speicherung sensibler Daten') }}</li>
                    <li>{{ __('Regelmässige Sicherheitsupdates') }}</li>
                    <li>{{ __('Zugangsbeschränkungen für Mitarbeiter') }}</li>
                </ul>

                <h2>7. {{ __('Ihre Rechte') }}</h2>
                <p>{{ __('Sie haben folgende Rechte bezüglich Ihrer personenbezogenen Daten:') }}</p>
                <ul>
                    <li>{{ __('Recht auf Auskunft über gespeicherte Daten') }}</li>
                    <li>{{ __('Recht auf Berichtigung unrichtiger Daten') }}</li>
                    <li>{{ __('Recht auf Löschung Ihrer Daten') }}</li>
                    <li>{{ __('Recht auf Datenübertragbarkeit') }}</li>
                    <li>{{ __('Recht auf Widerruf einer erteilten Einwilligung') }}</li>
                </ul>

                <h2>8. {{ __('Cookies') }}</h2>
                <p>{{ __('Unsere Website verwendet Cookies, um die Funktionalität zu gewährleisten:') }}</p>
                <ul>
                    <li>{{ __('Session-Cookies für die Anmeldung') }}</li>
                    <li>{{ __('CSRF-Schutz-Cookies') }}</li>
                    <li>{{ __('Optional: Analyse-Cookies (nur mit Einwilligung)') }}</li>
                </ul>

                <h2>9. {{ __('Aufbewahrungsdauer') }}</h2>
                <p>{{ __('Wir speichern Ihre Daten nur so lange, wie es für die Erfüllung der Zwecke erforderlich ist oder gesetzliche Aufbewahrungsfristen bestehen. Nach Löschung Ihres Kontos werden Ihre Daten innerhalb von 30 Tagen gelöscht, sofern keine gesetzlichen Aufbewahrungspflichten bestehen.') }}</p>

                <h2>10. {{ __('Kontakt') }}</h2>
                <p>{{ __('Bei Fragen zum Datenschutz wenden Sie sich bitte an:') }}</p>
                <p>
                    E-Mail: privacy@learningpilot.ch<br>
                    {{ __('oder nutzen Sie unser') }} <a href="{{ route('contact') }}" class="text-teal-600 hover:text-teal-500">{{ __('Kontaktformular') }}</a>
                </p>

                <h2>11. {{ __('Änderungen') }}</h2>
                <p>{{ __('Wir behalten uns vor, diese Datenschutzerklärung bei Bedarf anzupassen. Die aktuelle Version finden Sie stets auf dieser Seite.') }}</p>
            </div>
        </div>
    </div>
</x-layouts.public>
