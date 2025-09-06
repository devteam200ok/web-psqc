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
        'code', // QR 코드용 고유 식별자
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

    // 관계 설정
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function webTest(): BelongsTo
    {
        return $this->belongsTo(WebTest::class);
    }

    // 스코프
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

    // 접근자
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
        return $this->overall_score ? number_format($this->overall_score, 1) . '점' : 'N/A';
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
            return '결제 대기';
        }
        
        if ($this->payment_status === 'failed') {
            return '결제 실패';
        }
        
        if (!$this->is_valid) {
            return '무효화됨';
        }
        
        if ($this->is_expired) {
            return '만료됨';
        }
        
        return '유효';
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
            'pending' => '결제 대기',
            'paid' => '결제 완료',
            'failed' => '결제 실패',
            'refunded' => '환불됨',
            default => '알 수 없음'
        };
    }

    // 수정자
    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
        $this->attributes['domain'] = $this->extractDomain($value);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($certificate) {
            // 고유 코드 생성 (QR 코드용)
            $certificate->code = $certificate->generateUniqueCode();
            
            // 발급일시 설정
            $certificate->issued_at = now();
            
            // 만료일시는 결제 완료 후 설정하도록 변경
            if ($certificate->payment_status === 'paid') {
                $certificate->expires_at = now()->addYear();
                $certificate->is_valid = true;
            } else {
                $certificate->expires_at = null;
                $certificate->is_valid = false;
            }
        });
    }

    // 헬퍼 메서드
    private function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }

    private function generateUniqueCode(): string
    {
        do {
            // 12자리 랜덤 코드 생성 (숫자와 대문자)
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
        $this->update([
            'payment_status' => 'paid',
            'payment_data' => $paymentData,
            'expires_at' => now()->addYear(),
            'is_valid' => true
        ]);
    }

    public function markAsFailed(array $paymentData = []): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_data' => $paymentData
        ]);
    }

    public function refund(): void
    {
        $this->update([
            'payment_status' => 'refunded',
            'is_valid' => false
        ]);
    }

    public function renew(int $months = 12): void
    {
        $this->update([
            'expires_at' => now()->addMonths($months),
            'is_valid' => true
        ]);
    }

    // WebTest로부터 인증서 생성 (결제 대기 상태)
    public static function createFromWebTest(WebTest $webTest): self
    {
        // 로그인 사용자만 인증서 발급 가능
        if (!$webTest->user_id) {
            throw new \Exception('로그인이 필요합니다.');
        }

        // 이미 결제 완료된 인증서가 있는지 확인
        $existing = static::where('web_test_id', $webTest->id)
                          ->where('payment_status', 'paid')
                          ->where('is_valid', true)
                          ->first();
        
        if ($existing) {
            return $existing;
        }

        // 기존 결제 대기 중인 인증서가 있으면 삭제
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

    // 만료된/실패한 인증서 정리
    public static function cleanupOldCertificates(): int
    {
        $cleaned = 0;
        
        // 30일 이상 된 결제 실패 인증서 삭제
        $cleaned += static::where('payment_status', 'failed')
                          ->where('created_at', '<', now()->subDays(30))
                          ->delete();
        
        // 7일 이상 된 결제 대기 인증서 삭제
        $cleaned += static::where('payment_status', 'pending')
                          ->where('created_at', '<', now()->subDays(7))
                          ->delete();
        
        return $cleaned;
    }
}