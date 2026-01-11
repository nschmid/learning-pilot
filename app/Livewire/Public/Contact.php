<?php

namespace App\Livewire\Public;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Kontakt - LearningPilot')]
class Contact extends Component
{
    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('nullable|string|max:100')]
    public string $company = '';

    #[Validate('required|string|in:general,demo,support,partnership')]
    public string $subject = 'general';

    #[Validate('required|string|min:10|max:2000')]
    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        // Send notification to admin
        Mail::raw(
            "Neue Kontaktanfrage von {$this->name} ({$this->email})\n\n".
            "Firma: {$this->company}\n".
            "Betreff: {$this->getSubjectLabel()}\n\n".
            "Nachricht:\n{$this->message}",
            function ($message) {
                $message->to(config('mail.from.address'))
                    ->subject('Neue Kontaktanfrage: '.$this->getSubjectLabel());
            }
        );

        $this->submitted = true;
        $this->reset(['name', 'email', 'company', 'subject', 'message']);
    }

    public function render()
    {
        return view('livewire.public.contact', [
            'subjects' => $this->getSubjects(),
        ]);
    }

    protected function getSubjects(): array
    {
        return [
            'general' => __('Allgemeine Anfrage'),
            'demo' => __('Demo anfragen'),
            'support' => __('Technischer Support'),
            'partnership' => __('Partnerschaft'),
        ];
    }

    protected function getSubjectLabel(): string
    {
        return $this->getSubjects()[$this->subject] ?? $this->subject;
    }
}
