@component('mail::message')
# {{ __('Herzlichen Glückwunsch!') }}

{{ __('Hallo :name,', ['name' => $user->name]) }}

{{ __('Sie haben den Lernpfad ":title" erfolgreich abgeschlossen!', ['title' => $enrollment->learningPath->title]) }}

@component('mail::panel')
## {{ __('Ihre Leistung') }}

| | |
|---|---|
| {{ __('Fortschritt') }} | **100%** |
| {{ __('Punkte') }} | **{{ $enrollment->points_earned }}** |
| {{ __('Lernzeit') }} | **{{ $formattedDuration }}** |
| {{ __('Abgeschlossen am') }} | **{{ $enrollment->completed_at->format('d.m.Y') }}** |
@endcomponent

@if($certificate)
{{ __('Ihr Zertifikat wurde ausgestellt und kann jederzeit heruntergeladen werden.') }}

@component('mail::button', ['url' => route('learner.certificates.show', $certificate->id)])
{{ __('Zertifikat herunterladen') }}
@endcomponent
@else
@component('mail::button', ['url' => route('learner.catalog')])
{{ __('Weitere Lernpfade entdecken') }}
@endcomponent
@endif

{{ __('Weiter so! Bleiben Sie am Ball und erweitern Sie Ihr Wissen.') }}

{{ __('Mit freundlichen Grüssen,') }}

{{ __('Ihr LearningPilot Team') }}
@endcomponent
