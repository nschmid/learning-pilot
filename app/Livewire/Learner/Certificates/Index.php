<?php

namespace App\Livewire\Learner\Certificates;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Services\CertificateGeneratorService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public bool $showRequestModal = false;

    public ?string $selectedEnrollmentId = null;

    #[Computed]
    public function certificates(): Collection
    {
        return Certificate::whereHas('enrollment', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with(['enrollment.learningPath'])
            ->orderBy('issued_at', 'desc')
            ->get();
    }

    #[Computed]
    public function eligibleEnrollments(): Collection
    {
        $service = app(CertificateGeneratorService::class);

        return Enrollment::where('user_id', Auth::id())
            ->completed()
            ->whereDoesntHave('certificate')
            ->with('learningPath')
            ->get()
            ->filter(fn ($enrollment) => $service->canIssueCertificate($enrollment));
    }

    public function openRequestModal(): void
    {
        $this->showRequestModal = true;
    }

    public function closeRequestModal(): void
    {
        $this->showRequestModal = false;
        $this->selectedEnrollmentId = null;
    }

    public function requestCertificate(): void
    {
        if (! $this->selectedEnrollmentId) {
            return;
        }

        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('id', $this->selectedEnrollmentId)
            ->first();

        if (! $enrollment) {
            return;
        }

        $service = app(CertificateGeneratorService::class);

        try {
            $service->generate($enrollment);
            $this->closeRequestModal();
            unset($this->certificates);
            unset($this->eligibleEnrollments);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.learner.certificates.index')
            ->layout('layouts.learner', ['title' => __('Meine Zertifikate')]);
    }
}
