<?php

namespace App\Livewire\Learner\Certificates;

use App\Models\Certificate;
use App\Services\CertificateGeneratorService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Show extends Component
{
    public Certificate $certificate;

    public function mount(Certificate $certificate): void
    {
        $this->certificate = $certificate->load(['enrollment.user', 'enrollment.learningPath']);

        // Verify ownership
        if ($this->certificate->enrollment->user_id !== Auth::id()) {
            abort(403);
        }
    }

    public function download(): StreamedResponse
    {
        $service = app(CertificateGeneratorService::class);
        $content = $service->getPdfContent($this->certificate);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, "zertifikat-{$this->certificate->certificate_number}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function render()
    {
        return view('livewire.learner.certificates.show')
            ->layout('layouts.learner', ['title' => __('Zertifikat')]);
    }
}
