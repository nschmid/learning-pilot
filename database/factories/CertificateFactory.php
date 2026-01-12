<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory()->completed(),
            'certificate_number' => Certificate::generateCertificateNumber(),
            'issued_at' => now(),
            'expires_at' => now()->addYears(2),
            'pdf_path' => null,
            'metadata' => [],
        ];
    }

    /**
     * Indicate that the certificate has no expiration.
     */
    public function neverExpires(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => null,
        ]);
    }

    /**
     * Indicate that the certificate is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMonth(),
        ]);
    }

    /**
     * Set the PDF path.
     */
    public function withPdf(string $path = 'certificates/test.pdf'): static
    {
        return $this->state(fn (array $attributes) => [
            'pdf_path' => $path,
        ]);
    }

    /**
     * Create for specific enrollment.
     */
    public function forEnrollment(Enrollment $enrollment): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_id' => $enrollment->id,
        ]);
    }
}
