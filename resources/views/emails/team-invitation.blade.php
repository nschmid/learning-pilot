@component('mail::message')
# {{ __('Einladung zu :team', ['team' => $invitation->team->name]) }}

{{ __('Sie wurden eingeladen, dem Team ":team" auf LearningPilot beizutreten.', ['team' => $invitation->team->name]) }}

@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
{{ __('Falls Sie noch kein Konto haben, können Sie eines erstellen, indem Sie auf den Button unten klicken. Nach der Erstellung eines Kontos können Sie die Einladung mit dem zweiten Button annehmen:') }}

@component('mail::button', ['url' => route('register')])
{{ __('Konto erstellen') }}
@endcomponent

{{ __('Falls Sie bereits ein Konto haben, können Sie diese Einladung mit dem folgenden Button annehmen:') }}

@else
{{ __('Sie können diese Einladung mit dem folgenden Button annehmen:') }}
@endif

@component('mail::button', ['url' => $acceptUrl])
{{ __('Einladung annehmen') }}
@endcomponent

{{ __('Falls Sie diese Einladung nicht erwartet haben, können Sie diese E-Mail ignorieren.') }}
@endcomponent
