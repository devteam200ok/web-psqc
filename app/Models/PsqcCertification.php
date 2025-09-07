<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PsqcCertification extends Model
{
    use HasFactory;

    protected $table = 'psqc_certifications';

    protected $fillable = [
        'user_id',
        'url',
        'domain',
        'overall_grade',
        'overall_score',
        'metrics',
        'payment_status',
        'payment_data',
        'code',
        'issued_at',
        'expires_at',
        'is_valid',
        'is_revoked',
        'agency_name',
        'agency_representative',
    ];

    protected $casts = [
        'metrics'        => 'array',
        'payment_data'   => 'array',
        'overall_score'  => 'decimal:2',
        'is_valid'       => 'boolean',
        'is_revoked'     => 'boolean',
        'issued_at'      => 'datetime',
        'expires_at'     => 'datetime',
    ];

    /**
     * Owner (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Associated individual WebTests
     */
    public function webTests(): HasMany
    {
        return $this->hasMany(WebTest::class, 'psqc_certification_id');
    }

    /**
     * Whether the certification is currently valid
     */
    public function getIsCurrentlyValidAttribute(): bool
    {
        if ($this->is_revoked) {
            return false;
        }
        if (!$this->is_valid || !$this->issued_at) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        return true;
    }

    /**
     * Check if certification is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Create PSQC certification (pending status)
     */
    public static function createCertification(
        int $userId,
        string $url,
        string $domain,
        string $finalGrade,
        float $finalScore,
        array $tests
    ): self {
        // Check if a pending certification already exists
        $existingPending = self::where('user_id', $userId)
            ->where('url', $url)
            ->where('payment_status', 'pending')
            ->where('created_at', '>=', now()->subHours(1)) // within 1 hour
            ->first();

        if ($existingPending) {
            return $existingPending;
        }

        // Build metrics (including test IDs)
        $metrics = self::buildMetrics($tests);

        // Generate unique 12-character code
        $code = self::generateUniqueCode();

        // Create certification
        $certification = self::create([
            'user_id' => $userId,
            'url' => $url,
            'domain' => $domain,
            'overall_grade' => $finalGrade,
            'overall_score' => $finalScore,
            'metrics' => $metrics,
            'payment_status' => 'pending',
            'code' => $code,
            'is_valid' => false, // will be set to true after payment
            'is_revoked' => false,
            // issued_at, expires_at will be set after payment
        ]);

        // Linking WebTests will be done after payment
        return $certification;
    }

    /**
     * Build metrics
     */
    private static function buildMetrics(array $tests): array
    {
        $metrics = [
            'performance' => [],
            'security' => [],
            'quality' => [],
            'content' => [],
            'test_ids' => [], // related test IDs (linked after payment)
        ];

        foreach ($tests as $key => $test) {
            if (!$test) continue;

            $category = self::getCategoryFromTestType($key);
            
            $metrics[$category][$key] = [
                'score' => $test->overall_score,
                'grade' => $test->overall_grade,
                'test_id' => $test->id,
                'finished_at' => $test->finished_at,
            ];

            $metrics['test_ids'][] = $test->id;
        }

        return $metrics;
    }

    /**
     * Extract category from test type
     */
    private static function getCategoryFromTestType(string $testType): string
    {
        $prefix = substr($testType, 0, 1);
        
        return match($prefix) {
            'p' => 'performance',
            's' => 'security',
            'q' => 'quality',
            'c' => 'content',
            default => 'other'
        };
    }

    /**
     * Generate unique code
     */
    private static function generateUniqueCode(): string
    {
        do {
            // Generate 12-character alphanumeric uppercase code
            $code = strtoupper(Str::random(12));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Link WebTest records to the certification (after payment is completed)
     */
    public static function linkWebTests(int $certificationId, array $testIds): void
    {
        if (!empty($testIds)) {
            WebTest::whereIn('id', $testIds)
                ->whereNull('psqc_certification_id') // only those not yet linked
                ->update(['psqc_certification_id' => $certificationId]);
        }
    }

    /**
     * Handle payment completion
     */
    public function markAsPaid(array $paymentData = []): void
    {
        $updateData = [
            'payment_status' => 'paid',
            'payment_data' => $paymentData,
            'is_valid' => true,
            'issued_at' => now(),
            'expires_at' => now()->addYear(), // valid for 1 year
        ];

        // PayPal 결제 정보가 있는 경우 추가 처리
        if (isset($paymentData['paypal_order_id'])) {
            $updateData['payment_data'] = array_merge($paymentData, [
                'payment_method' => 'paypal',
                'paid_at' => now()->toDateTimeString(),
            ]);
        }

        $this->update($updateData);

        // Use stored test_ids in metrics to link WebTests
        if (isset($this->metrics['test_ids']) && !empty($this->metrics['test_ids'])) {
            self::linkWebTests($this->id, $this->metrics['test_ids']);
        }
    }

    /**
     * Handle payment failure
     */
    public function markAsFailed(array $failureData = []): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_data' => array_merge($this->payment_data ?? [], [
                'failed_at' => now()->toDateTimeString(),
                'failure_data' => $failureData,
            ]),
        ]);
    }

    /**
     * Revoke certification
     */
    public function revoke(string $reason = null): void
    {
        $this->update([
            'is_revoked' => true,
            'is_valid' => false,
            'payment_data' => array_merge($this->payment_data ?? [], [
                'revoked_at' => now()->toDateTimeString(),
                'revoked_reason' => $reason,
            ]),
        ]);
    }

    public function getGradeColorAttribute(): string
    {
        return match($this->overall_grade) {
            'A+' => 'bg-green-lt text-green-lt-fg',
            'A' => 'bg-lime-lt text-lime-lt-fg',
            'B' => 'bg-blue-lt text-blue-lt-fg', 
            'C' => 'bg-yellow-lt text-yellow-lt-fg',
            'D' => 'bg-orange-lt text-orange-lt-fg',
            'F' => 'bg-red-lt text-red-lt-fg',
            default => 'bg-azure-lt text-azure-lt-fg'
        };
    }

    public function getFormattedScoreAttribute(): string
    {
        return $this->overall_score ? number_format($this->overall_score, 1) . ' pts' : 'N/A';
    }

    /**
     * Get labels for test types
     */
    public function getTestTypesAttribute(): array
    {
        return WebTest::getTestTypes();
    }

    public function getStatusAttribute(): string
    {
        if ($this->payment_status === 'pending') {
            return 'Pending Payment';
        }
        
        if ($this->payment_status === 'failed') {
            return 'Payment Failed';
        }
        
        if (!$this->is_valid) {
            return 'Invalidated';
        }
        
        if ($this->is_expired) {
            return 'Expired';
        }
        
        return 'Valid';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->payment_status === 'pending') {
            return 'badge bg-orange-lt text-orange-lt-fg';
        }
        
        if ($this->payment_status === 'failed') {
            return 'badge bg-red-lt text-red-lt-fg';
        }
        
        if (!$this->is_valid) {
            return 'badge bg-red-lt text-red-lt-fg';
        }
        
        if ($this->is_expired) {
            return 'badge bg-orange-lt text-orange-lt-fg';
        }
        
        return 'badge bg-green-lt text-green-lt-fg';
    }
}