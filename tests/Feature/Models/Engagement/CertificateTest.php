<?php

namespace Tests\Feature\Models\Engagement;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class CertificateTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_certificate(): void
    {
        $certificate = $this->assertModelCanBeCreated(Certificate::class);

        $this->assertNotNull($certificate->certificate_number);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $certificate = Certificate::factory()->create();

        $this->assertFillableFieldsExist($certificate);
    }

    public function test_uses_uuids(): void
    {
        $certificate = Certificate::factory()->create();

        $this->assertUsesUuids($certificate);
    }

    public function test_datetime_casts_work(): void
    {
        $certificate = Certificate::factory()->create();

        $this->assertDatetimeCast($certificate, 'issued_at');
        $this->assertDatetimeCast($certificate, 'expires_at');
    }

    public function test_array_casts_work(): void
    {
        $certificate = Certificate::factory()->create([
            'metadata' => ['course_name' => 'Test Course'],
        ]);

        $this->assertArrayCast($certificate, 'metadata');
    }

    public function test_enrollment_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->completed()->create();
        $certificate = Certificate::factory()->forEnrollment($enrollment)->create();

        $this->assertBelongsToRelationship($certificate, 'enrollment', Enrollment::class);
    }

    public function test_is_valid_helper_works(): void
    {
        $valid = Certificate::factory()->create(['expires_at' => now()->addYear()]);
        $expired = Certificate::factory()->expired()->create();
        $neverExpires = Certificate::factory()->neverExpires()->create();

        $this->assertTrue($valid->isValid());
        $this->assertFalse($expired->isValid());
        $this->assertTrue($neverExpires->isValid());
    }

    public function test_is_expired_helper_works(): void
    {
        $valid = Certificate::factory()->create(['expires_at' => now()->addYear()]);
        $expired = Certificate::factory()->expired()->create();

        $this->assertFalse($valid->isExpired());
        $this->assertTrue($expired->isExpired());
    }

    public function test_generate_certificate_number_creates_unique_number(): void
    {
        $number1 = Certificate::generateCertificateNumber();
        $number2 = Certificate::generateCertificateNumber();

        $this->assertStringStartsWith('LP-', $number1);
        $this->assertNotEquals($number1, $number2);
    }
}
