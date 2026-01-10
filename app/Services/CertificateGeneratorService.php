<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Certificate;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateGeneratorService
{
    /**
     * Check if a certificate can be issued for the enrollment.
     */
    public function canIssueCertificate(Enrollment $enrollment): bool
    {
        // Must be completed
        if ($enrollment->status !== EnrollmentStatus::Completed) {
            return false;
        }

        // Must have 100% progress (or configured minimum)
        $minProgress = config('lernpfad.certificates.min_progress_percent', 100);
        if ($enrollment->progress_percent < $minProgress) {
            return false;
        }

        // Check if all assessments are passed (if required)
        if (config('lernpfad.certificates.require_all_assessments_passed', true)) {
            $failedAssessments = $enrollment->assessmentAttempts()
                ->where('passed', false)
                ->whereDoesntHave('assessment', function ($query) {
                    // Check if there's a later passing attempt
                })
                ->exists();

            // Get unique assessments and check each has a passing attempt
            $assessmentIds = $enrollment->learningPath
                ->steps()
                ->whereHas('assessment')
                ->with('assessment')
                ->get()
                ->pluck('assessment.id')
                ->filter();

            foreach ($assessmentIds as $assessmentId) {
                $passed = $enrollment->assessmentAttempts()
                    ->where('assessment_id', $assessmentId)
                    ->where('passed', true)
                    ->exists();

                if (! $passed) {
                    return false;
                }
            }
        }

        // Certificate must not already exist
        if ($enrollment->certificate()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get the reason why a certificate cannot be issued.
     */
    public function getBlockingReason(Enrollment $enrollment): ?string
    {
        if ($enrollment->status !== EnrollmentStatus::Completed) {
            return __('Der Lernpfad wurde noch nicht abgeschlossen.');
        }

        $minProgress = config('lernpfad.certificates.min_progress_percent', 100);
        if ($enrollment->progress_percent < $minProgress) {
            return __('Der Fortschritt muss mindestens :percent% betragen.', ['percent' => $minProgress]);
        }

        if ($enrollment->certificate()->exists()) {
            return __('Ein Zertifikat wurde bereits ausgestellt.');
        }

        if (config('lernpfad.certificates.require_all_assessments_passed', true)) {
            $assessmentIds = $enrollment->learningPath
                ->steps()
                ->whereHas('assessment')
                ->with('assessment')
                ->get()
                ->pluck('assessment.id')
                ->filter();

            foreach ($assessmentIds as $assessmentId) {
                $passed = $enrollment->assessmentAttempts()
                    ->where('assessment_id', $assessmentId)
                    ->where('passed', true)
                    ->exists();

                if (! $passed) {
                    return __('Nicht alle Prüfungen wurden bestanden.');
                }
            }
        }

        return null;
    }

    /**
     * Generate and store a certificate for the enrollment.
     */
    public function generate(Enrollment $enrollment): Certificate
    {
        if (! $this->canIssueCertificate($enrollment)) {
            throw new \Exception($this->getBlockingReason($enrollment) ?? __('Zertifikat kann nicht ausgestellt werden.'));
        }

        $enrollment->load(['user', 'learningPath']);

        // Generate certificate number
        $certificateNumber = Certificate::generateCertificateNumber();

        // Calculate expiration date
        $validityYears = config('lernpfad.defaults.certificate_validity_years', 2);
        $expiresAt = $validityYears > 0 ? now()->addYears($validityYears) : null;

        // Generate verification URL
        $verificationUrl = config('lernpfad.certificates.verification_url') . '/' . $certificateNumber;

        // Build metadata
        $metadata = [
            'learner_name' => $enrollment->user->name,
            'learner_email' => $enrollment->user->email,
            'path_title' => $enrollment->learningPath->title,
            'path_description' => $enrollment->learningPath->description,
            'completed_at' => $enrollment->completed_at?->format('d.m.Y'),
            'total_time_spent' => $enrollment->getFormattedTimeSpent(),
            'points_earned' => $enrollment->points_earned,
            'issued_at' => now()->format('d.m.Y'),
            'expires_at' => $expiresAt?->format('d.m.Y'),
            'verification_url' => $verificationUrl,
        ];

        // Generate PDF
        $pdf = $this->generatePdf($enrollment, $certificateNumber, $metadata);

        // Store PDF
        $pdfPath = $this->storePdf($pdf, $certificateNumber);

        // Create certificate record
        return Certificate::create([
            'enrollment_id' => $enrollment->id,
            'certificate_number' => $certificateNumber,
            'issued_at' => now(),
            'expires_at' => $expiresAt,
            'pdf_path' => $pdfPath,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Generate the PDF for the certificate.
     */
    protected function generatePdf(Enrollment $enrollment, string $certificateNumber, array $metadata): \Barryvdh\DomPDF\PDF
    {
        $includeQrCode = config('lernpfad.certificates.include_qr_code', true);

        $data = [
            'enrollment' => $enrollment,
            'user' => $enrollment->user,
            'learningPath' => $enrollment->learningPath,
            'certificateNumber' => $certificateNumber,
            'metadata' => $metadata,
            'includeQrCode' => $includeQrCode,
            'qrCodeUrl' => $includeQrCode ? $this->generateQrCodeUrl($metadata['verification_url']) : null,
        ];

        return Pdf::loadView('certificates.pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);
    }

    /**
     * Store the PDF file and return the path.
     */
    protected function storePdf(\Barryvdh\DomPDF\PDF $pdf, string $certificateNumber): string
    {
        $filename = "certificates/{$certificateNumber}.pdf";

        Storage::disk('local')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate a QR code URL for verification.
     */
    protected function generateQrCodeUrl(string $verificationUrl): string
    {
        // Using Google Charts API for QR code generation
        $encodedUrl = urlencode($verificationUrl);

        return "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={$encodedUrl}&choe=UTF-8";
    }

    /**
     * Regenerate the PDF for an existing certificate.
     */
    public function regeneratePdf(Certificate $certificate): Certificate
    {
        $enrollment = $certificate->enrollment;
        $enrollment->load(['user', 'learningPath']);

        // Regenerate PDF
        $pdf = $this->generatePdf($enrollment, $certificate->certificate_number, $certificate->metadata);

        // Store PDF (overwrites existing)
        $pdfPath = $this->storePdf($pdf, $certificate->certificate_number);

        $certificate->update(['pdf_path' => $pdfPath]);

        return $certificate->fresh();
    }

    /**
     * Get the PDF content for download or viewing.
     */
    public function getPdfContent(Certificate $certificate): string
    {
        if (Storage::disk('local')->exists($certificate->pdf_path)) {
            return Storage::disk('local')->get($certificate->pdf_path);
        }

        // Regenerate if file doesn't exist
        $this->regeneratePdf($certificate);

        return Storage::disk('local')->get($certificate->pdf_path);
    }

    /**
     * Verify a certificate by its number.
     */
    public function verify(string $certificateNumber): ?array
    {
        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->with(['enrollment.user', 'enrollment.learningPath'])
            ->first();

        if (! $certificate) {
            return null;
        }

        return [
            'valid' => $certificate->isValid(),
            'certificate' => $certificate,
            'learner_name' => $certificate->enrollment->user->name,
            'learning_path' => $certificate->enrollment->learningPath->title,
            'issued_at' => $certificate->issued_at,
            'expires_at' => $certificate->expires_at,
            'is_expired' => $certificate->isExpired(),
        ];
    }
}
