<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'web_test_id',
        'test_type',
        'url',
        'domain',
        'code', // Unique identifier for QR code
        'overall_grade',
        'overall_score',
        'payment_status',
        'payment_data',
        'issued_at',
        'expires_at',
        'is_valid',
    ];

    protected $casts = [
        'payment_data' => 'array',
        'overall_score' => 'decimal:2',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_valid' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function webTest(): BelongsTo
    {
        return $this->belongsTo(WebTest::class);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('is_valid', true)
                    ->where('expires_at', '>', now());
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeByTestType($query, string $testType)
    {
        return $query->where('test_type', $testType);
    }

    public function scopeByDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    public function scopeByGrade($query, array $grades)
    {
        return $query->whereIn('overall_grade', $grades);
    }

    // Accessors
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

    public function getShortDomainAttribute(): string
    {
        return str_replace(['https://', 'http://', 'www.'], '', $this->domain);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
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

    public function getQrCodeUrlAttribute(): string
    {
        return route('certificate.public', ['code' => $this->code]);
    }

    public function getTestTypeNameAttribute(): string
    {
        $testTypes = WebTest::getTestTypes();
        return $testTypes[$this->test_type] ?? $this->test_type;
    }

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }
        
        $days = now()->diffInDays($this->expires_at, false);
        return $days > 0 ? $days : 0;
    }

    public function getPaymentStatusTextAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pending Payment',
            'paid' => 'Payment Completed',
            'failed' => 'Payment Failed',
            'refunded' => 'Refunded',
            default => 'Unknown'
        };
    }

    // Mutators
    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
        $this->attributes['domain'] = $this->extractDomain($value);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($certificate) {
            // Generate unique code (for QR)
            $certificate->code = $certificate->generateUniqueCode();
            
            // Set issue date
            $certificate->issued_at = now();
            
            // Expiration date is set only after payment is completed
            if ($certificate->payment_status === 'paid') {
                $certificate->expires_at = now()->addYear();
                $certificate->is_valid = true;
            } else {
                $certificate->expires_at = null;
                $certificate->is_valid = false;
            }
        });
    }

    // Helper methods
    private function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }

    private function generateUniqueCode(): string
    {
        do {
            // Generate 12-character random code (uppercase letters & digits)
            $code = strtoupper(Str::random(12));
        } while (static::where('code', $code)->exists());
        
        return $code;
    }

    public function invalidate(): void
    {
        $this->update(['is_valid' => false]);
    }

    public function markAsPaid(array $paymentData = []): void
    {
        $updateData = [
            'payment_status' => 'paid',
            'payment_data' => $paymentData,
            'expires_at' => now()->addYear(),
            'is_valid' => true
        ];

        // PayPal 결제 정보가 있는 경우 추가 처리
        if (isset($paymentData['paypal_order_id'])) {
            $updateData['payment_data'] = array_merge($paymentData, [
                'payment_method' => 'paypal',
                'paid_at' => now()->toDateTimeString(),
            ]);
        }

        $this->update($updateData);
    }

    public function markAsFailed(array $paymentData = []): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_data' => array_merge($this->payment_data ?? [], [
                'failed_at' => now()->toDateTimeString(),
                'failure_data' => $paymentData,
            ])
        ]);
    }

    public function refund(): void
    {
        $this->update([
            'payment_status' => 'refunded',
            'is_valid' => false,
            'payment_data' => array_merge($this->payment_data ?? [], [
                'refunded_at' => now()->toDateTimeString(),
            ])
        ]);
    }

    public function renew(int $months = 12): void
    {
        $this->update([
            'expires_at' => now()->addMonths($months),
            'is_valid' => true
        ]);
    }

    // Generate certificate from WebTest (initially pending payment)
    public static function createFromWebTest(WebTest $webTest): self
    {
        // Only logged-in users can issue a certificate
        if (!$webTest->user_id) {
            throw new \Exception('Login required.');
        }

        // Check if a paid and valid certificate already exists
        $existing = static::where('web_test_id', $webTest->id)
                          ->where('payment_status', 'paid')
                          ->where('is_valid', true)
                          ->first();
        
        if ($existing) {
            return $existing;
        }

        // Remove any existing pending certificates
        static::where('web_test_id', $webTest->id)
              ->where('payment_status', 'pending')
              ->delete();

        return static::create([
            'user_id' => $webTest->user_id,
            'web_test_id' => $webTest->id,
            'test_type' => $webTest->test_type,
            'url' => $webTest->url,
            'overall_grade' => $webTest->overall_grade,
            'overall_score' => $webTest->overall_score,
            'payment_status' => 'pending',
        ]);
    }

    // Cleanup expired/failed certificates
    public static function cleanupOldCertificates(): int
    {
        $cleaned = 0;
        
        // Delete failed certificates older than 30 days
        $cleaned += static::where('payment_status', 'failed')
                          ->where('created_at', '<', now()->subDays(30))
                          ->delete();
        
        // Delete pending certificates older than 7 days
        $cleaned += static::where('payment_status', 'pending')
                          ->where('created_at', '<', now()->subDays(7))
                          ->delete();
        
        return $cleaned;
    }
}