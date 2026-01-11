<x-layouts.public>
    <x-slot name="title">{{ __('Allgemeine Geschäftsbedingungen') }} - LearningPilot</x-slot>

    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-3xl px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Allgemeine Geschäftsbedingungen') }}</h1>
            <p class="mt-4 text-sm text-gray-500">{{ __('Stand: Januar 2025') }}</p>

            <div class="mt-10 prose prose-indigo max-w-none">
                <h2>1. {{ __('Geltungsbereich') }}</h2>
                <p>{{ __('Diese Allgemeinen Geschäftsbedingungen (AGB) gelten für alle Verträge zwischen LearningPilot und Nutzern der Plattform. Mit der Registrierung akzeptieren Sie diese AGB.') }}</p>

                <h2>2. {{ __('Leistungsbeschreibung') }}</h2>
                <p>{{ __('LearningPilot ist eine webbasierte Lernplattform, die folgende Leistungen umfasst:') }}</p>
                <ul>
                    <li>{{ __('Erstellung und Verwaltung von Lernpfaden') }}</li>
                    <li>{{ __('Durchführung von Online-Assessments') }}</li>
                    <li>{{ __('Fortschrittsverfolgung und Analyse') }}</li>
                    <li>{{ __('Ausstellung von Zertifikaten') }}</li>
                    <li>{{ __('KI-gestützte Lernunterstützung') }}</li>
                </ul>

                <h2>3. {{ __('Registrierung und Konto') }}</h2>
                <h3>3.1 {{ __('Registrierung') }}</h3>
                <p>{{ __('Zur Nutzung der Plattform ist eine Registrierung erforderlich. Sie müssen wahrheitsgemässe Angaben machen und mindestens 16 Jahre alt sein.') }}</p>

                <h3>3.2 {{ __('Kontosicherheit') }}</h3>
                <p>{{ __('Sie sind für die Sicherheit Ihrer Zugangsdaten verantwortlich. Teilen Sie Ihr Passwort nicht mit anderen Personen.') }}</p>

                <h3>3.3 {{ __('Kündigung') }}</h3>
                <p>{{ __('Sie können Ihr Konto jederzeit in den Kontoeinstellungen löschen. Bei kostenpflichtigen Abonnements endet das Abonnement zum Ende der bezahlten Periode.') }}</p>

                <h2>4. {{ __('Preise und Zahlung') }}</h2>
                <h3>4.1 {{ __('Preise') }}</h3>
                <p>{{ __('Die aktuellen Preise finden Sie auf unserer') }} <a href="{{ route('pricing') }}" class="text-indigo-600 hover:text-indigo-500">{{ __('Preisseite') }}</a>. {{ __('Alle Preise verstehen sich inklusive der gesetzlichen Mehrwertsteuer.') }}</p>

                <h3>4.2 {{ __('Testphase') }}</h3>
                <p>{{ __('Neue Nutzer erhalten eine kostenlose Testphase von 30 Tagen. Nach Ablauf wird das Konto pausiert, bis ein kostenpflichtiges Abonnement abgeschlossen wird.') }}</p>

                <h3>4.3 {{ __('Zahlungsbedingungen') }}</h3>
                <p>{{ __('Die Zahlung erfolgt im Voraus per Kreditkarte oder SEPA-Lastschrift. Rechnungen werden per E-Mail zugestellt.') }}</p>

                <h3>4.4 {{ __('Preisänderungen') }}</h3>
                <p>{{ __('Preisänderungen werden mindestens 30 Tage im Voraus angekündigt und gelten ab der nächsten Abrechnungsperiode.') }}</p>

                <h2>5. {{ __('Nutzungsrechte und -pflichten') }}</h2>
                <h3>5.1 {{ __('Erlaubte Nutzung') }}</h3>
                <p>{{ __('Sie dürfen die Plattform für Ihre eigenen Lernzwecke oder zur Schulung Ihrer Organisation nutzen.') }}</p>

                <h3>5.2 {{ __('Verbotene Nutzung') }}</h3>
                <p>{{ __('Folgende Nutzungen sind untersagt:') }}</p>
                <ul>
                    <li>{{ __('Weitergabe von Zugangsdaten an Dritte') }}</li>
                    <li>{{ __('Automatisierter Zugriff oder Scraping') }}</li>
                    <li>{{ __('Upload von rechtswidrigen oder anstössigen Inhalten') }}</li>
                    <li>{{ __('Versuch, die Sicherheitsmassnahmen zu umgehen') }}</li>
                    <li>{{ __('Nutzung zum Nachteil anderer Nutzer') }}</li>
                </ul>

                <h2>6. {{ __('Geistiges Eigentum') }}</h2>
                <h3>6.1 {{ __('Plattform') }}</h3>
                <p>{{ __('Die Plattform und alle zugehörigen Inhalte sind urheberrechtlich geschützt. Sie erhalten ein einfaches, nicht übertragbares Nutzungsrecht für die Dauer Ihres Abonnements.') }}</p>

                <h3>6.2 {{ __('Nutzererstellte Inhalte') }}</h3>
                <p>{{ __('Sie behalten alle Rechte an Inhalten, die Sie erstellen. Sie gewähren LearningPilot eine Lizenz zur Speicherung und Anzeige dieser Inhalte im Rahmen des Dienstes.') }}</p>

                <h2>7. {{ __('Verfügbarkeit und Support') }}</h2>
                <h3>7.1 {{ __('Verfügbarkeit') }}</h3>
                <p>{{ __('Wir bemühen uns um eine hohe Verfügbarkeit der Plattform, garantieren jedoch keine 100%ige Verfügbarkeit. Geplante Wartungsarbeiten werden im Voraus angekündigt.') }}</p>

                <h3>7.2 {{ __('Support') }}</h3>
                <p>{{ __('Support ist per E-Mail erreichbar. Die Antwortzeit variiert je nach gewähltem Plan.') }}</p>

                <h2>8. {{ __('Haftung') }}</h2>
                <h3>8.1 {{ __('Haftungsbeschränkung') }}</h3>
                <p>{{ __('LearningPilot haftet nur für Schäden, die durch vorsätzliches oder grob fahrlässiges Verhalten verursacht wurden. Die Haftung für leichte Fahrlässigkeit ist ausgeschlossen, soweit gesetzlich zulässig.') }}</p>

                <h3>8.2 {{ __('Datenverlust') }}</h3>
                <p>{{ __('Wir führen regelmässige Backups durch, übernehmen jedoch keine Haftung für Datenverluste. Wir empfehlen, wichtige Daten zusätzlich zu sichern.') }}</p>

                <h2>9. {{ __('Datenschutz') }}</h2>
                <p>{{ __('Die Verarbeitung personenbezogener Daten erfolgt gemäss unserer') }} <a href="{{ route('legal.privacy') }}" class="text-indigo-600 hover:text-indigo-500">{{ __('Datenschutzerklärung') }}</a>.</p>

                <h2>10. {{ __('Änderungen der AGB') }}</h2>
                <p>{{ __('Wir behalten uns vor, diese AGB zu ändern. Wesentliche Änderungen werden mindestens 30 Tage im Voraus per E-Mail angekündigt. Bei Widerspruch können Sie das Abonnement kündigen.') }}</p>

                <h2>11. {{ __('Schlussbestimmungen') }}</h2>
                <h3>11.1 {{ __('Anwendbares Recht') }}</h3>
                <p>{{ __('Es gilt schweizerisches Recht unter Ausschluss der Kollisionsnormen.') }}</p>

                <h3>11.2 {{ __('Gerichtsstand') }}</h3>
                <p>{{ __('Gerichtsstand ist Zürich, Schweiz.') }}</p>

                <h3>11.3 {{ __('Salvatorische Klausel') }}</h3>
                <p>{{ __('Sollten einzelne Bestimmungen unwirksam sein, bleibt die Gültigkeit der übrigen Bestimmungen unberührt.') }}</p>

                <h2>12. {{ __('Kontakt') }}</h2>
                <p>
                    LearningPilot<br>
                    Musterstrasse 123<br>
                    8000 Zürich<br>
                    Schweiz<br>
                    E-Mail: legal@learningpilot.ch
                </p>
            </div>
        </div>
    </div>
</x-layouts.public>
