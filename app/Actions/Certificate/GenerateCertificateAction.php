<?php

namespace App\Actions\Certificate;

use App\Events\CertificateIssued;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Services\CertificateGeneratorService;
use Illuminate\Support\Str;

class GenerateCertificateAction
{
    public function __construct(
        protected CertificateGeneratorService $certificateService
    ) {}

    /**
     * Generate a certificate for a completed enrollment.
     */
    public function execute(Enrollment $enrollment): Certificate
    {
        // Check if certificate already exists
        $existingCertificate = Certificate::where('enrollment_id', $enrollment->id)->first();
        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Generate unique certificate number
        $certificateNumber = $this->generateCertificateNumber();

        // Create certificate record
        $certificate = Certificate::create([
            'enrollment_id' => $enrollment->id,
            'certificate_number' => $certificateNumber,
            'issued_at' => now(),
            'expires_at' => $this->calculateExpiryDate(),
        ]);

        // Generate PDF
        $pdfPath = $this->certificateService->generate($certificate);

        $certificate->update([
            'pdf_path' => $pdfPath,
        ]);

        // Dispatch event
        event(new CertificateIssued($certificate));

        activity()
            ->performedOn($certificate)
            ->causedBy($enrollment->user)
            ->log('certificate issued');

        return $certificate;
    }

    /**
     * Generate a unique certificate number.
     */
    protected function generateCertificateNumber(): string
    {
        do {
            $number = 'LP-' . strtoupper(Str::random(8));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Calculate certificate expiry date.
     */
    protected function calculateExpiryDate(): ?\DateTime
    {
        $validityYears = config('lernpfad.defaults.certificate_validity_years', 2);

        if ($validityYears === null) {
            return null; // No expiry
        }

        return now()->addYears($validityYears);
    }

    /**
     * Regenerate certificate PDF.
     */
    public function regenerate(Certificate $certificate): Certificate
    {
        $pdfPath = $this->certificateService->generate($certificate);

        $certificate->update([
            'pdf_path' => $pdfPath,
        ]);

        return $certificate->fresh();
    }

    /**
     * Verify a certificate by its number.
     */
    public function verify(string $certificateNumber): ?Certificate
    {
        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->with(['enrollment.user', 'enrollment.learningPath'])
            ->first();

        if (! $certificate) {
            return null;
        }

        // Check if expired
        if ($certificate->expires_at && $certificate->expires_at->isPast()) {
            return null;
        }

        return $certificate;
    }
}
