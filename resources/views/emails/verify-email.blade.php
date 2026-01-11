@component('mail::message')
# {{ __('E-Mail-Adresse bestätigen') }}

{{ __('Hallo :name,', ['name' => $user->name]) }}

{{ __('Bitte klicken Sie auf den Button unten, um Ihre E-Mail-Adresse zu bestätigen.') }}

@component('mail::button', ['url' => $verificationUrl])
{{ __('E-Mail bestätigen') }}
@endcomponent

{{ __('Dieser Link ist 60 Minuten gültig.') }}

{{ __('Falls Sie kein Konto bei LearningPilot erstellt haben, können Sie diese E-Mail ignorieren.') }}

{{ __('Mit freundlichen Grüssen,') }}

{{ __('Ihr LearningPilot Team') }}
@endcomponent
