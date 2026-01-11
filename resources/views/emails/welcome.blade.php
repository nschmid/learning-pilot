@component('mail::message')
# {{ __('Willkommen bei LearningPilot!') }}

{{ __('Hallo :name,', ['name' => $user->name]) }}

{{ __('Vielen Dank für Ihre Registrierung bei LearningPilot! Wir freuen uns, Sie an Bord zu haben.') }}

{{ __('Mit LearningPilot können Sie:') }}

- {{ __('Lernpfade erstellen und verwalten') }}
- {{ __('Assessments und Prüfungen durchführen') }}
- {{ __('Den Fortschritt Ihrer Lernenden verfolgen') }}
- {{ __('KI-gestützte Lernunterstützung nutzen') }}
- {{ __('Professionelle Zertifikate ausstellen') }}

@component('mail::button', ['url' => route('dashboard')])
{{ __('Jetzt starten') }}
@endcomponent

{{ __('Ihre 30-tägige kostenlose Testphase beginnt jetzt. Nutzen Sie diese Zeit, um alle Funktionen kennenzulernen.') }}

{{ __('Viel Erfolg!') }}

{{ __('Ihr LearningPilot Team') }}
@endcomponent
