@component('mail::message')
# {{ __('Ihr Zertifikat ist bereit!') }}

{{ __('Hallo :name,', ['name' => $user->name]) }}

{{ __('Herzlichen Glückwunsch! Sie haben den Lernpfad ":title" erfolgreich abgeschlossen.', ['title' => $certificate->enrollment->learningPath->title]) }}

{{ __('Ihr offizielles Zertifikat wurde ausgestellt.') }}

@component('mail::panel')
**{{ __('Zertifikatsnummer') }}:** {{ $certificate->certificate_number }}

**{{ __('Ausgestellt am') }}:** {{ $certificate->issued_at->format('d.m.Y') }}

@if($certificate->expires_at)
**{{ __('Gültig bis') }}:** {{ $certificate->expires_at->format('d.m.Y') }}
@endif
@endcomponent

@component('mail::button', ['url' => route('learner.certificates.show', $certificate->id)])
{{ __('Zertifikat herunterladen') }}
@endcomponent

{{ __('Das Zertifikat kann von Dritten über folgenden Link verifiziert werden:') }}

[{{ route('certificate.verify', $certificate->certificate_number) }}]({{ route('certificate.verify', $certificate->certificate_number) }})

{{ __('Mit freundlichen Grüssen,') }}

{{ __('Ihr LearningPilot Team') }}
@endcomponent
