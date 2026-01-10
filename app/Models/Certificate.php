<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'enrollment_id',
        'certificate_number',
        'issued_at',
        'expires_at',
        'pdf_path',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // Relationships

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Helpers

    public function isValid(): bool
    {
        if (! $this->expires_at) {
            return true;
        }

        return $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return ! $this->isValid();
    }

    public static function generateCertificateNumber(): string
    {
        $prefix = 'LP';
        $year = now()->format('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 8));

        return "{$prefix}-{$year}-{$random}";
    }
}
