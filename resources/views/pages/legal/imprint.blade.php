<x-layouts.public>
    <x-slot name="title">{{ __('Impressum') }} - LearningPilot</x-slot>

    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-3xl px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Impressum') }}</h1>

            <div class="mt-10 prose prose-indigo max-w-none">
                <h2>{{ __('Angaben gemäss Schweizer Recht') }}</h2>

                <h3>{{ __('Betreiber der Plattform') }}</h3>
                <p>
                    <strong>LearningPilot</strong><br>
                    Musterstrasse 123<br>
                    8000 Zürich<br>
                    Schweiz
                </p>

                <h3>{{ __('Kontakt') }}</h3>
                <p>
                    {{ __('Telefon') }}: +41 44 123 45 67<br>
                    {{ __('E-Mail') }}: hello@learningpilot.ch<br>
                    {{ __('Website') }}: www.learningpilot.ch
                </p>

                <h3>{{ __('Vertretungsberechtigte Personen') }}</h3>
                <p>
                    Max Muster, {{ __('Geschäftsführer') }}
                </p>

                <h3>{{ __('Handelsregister') }}</h3>
                <p>
                    {{ __('Eingetragen im Handelsregister des Kantons Zürich') }}<br>
                    {{ __('Firmennummer') }}: CHE-123.456.789
                </p>

                <h3>{{ __('Mehrwertsteuernummer') }}</h3>
                <p>CHE-123.456.789 MWST</p>

                <h2>{{ __('Haftungsausschluss') }}</h2>

                <h3>{{ __('Inhalt der Website') }}</h3>
                <p>{{ __('Die Inhalte unserer Seiten wurden mit grösster Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen.') }}</p>

                <h3>{{ __('Links zu externen Websites') }}</h3>
                <p>{{ __('Unsere Website enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber verantwortlich. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.') }}</p>

                <h3>{{ __('Urheberrecht') }}</h3>
                <p>{{ __('Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem schweizerischen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung ausserhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.') }}</p>

                <h2>{{ __('Datenschutz') }}</h2>
                <p>{{ __('Informationen zur Verarbeitung personenbezogener Daten finden Sie in unserer') }} <a href="{{ route('legal.privacy') }}" class="text-teal-600 hover:text-teal-500">{{ __('Datenschutzerklärung') }}</a>.</p>

                <h2>{{ __('Streitbeilegung') }}</h2>
                <p>{{ __('Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit. Wir sind nicht verpflichtet und nicht bereit, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.') }}</p>

                <h2>{{ __('Redaktionell verantwortlich') }}</h2>
                <p>
                    Max Muster<br>
                    Musterstrasse 123<br>
                    8000 Zürich<br>
                    Schweiz
                </p>
            </div>
        </div>
    </div>
</x-layouts.public>
