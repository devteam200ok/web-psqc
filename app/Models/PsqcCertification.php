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
     * 소유자 (사용자)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 포함된 개별 WebTest들
     */
    public function webTests(): HasMany
    {
        return $this->hasMany(WebTest::class, 'psqc_certification_id');
    }

    /**
     * 인증서가 현재 유효한지 여부
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
     * PSQC 인증서 생성 (pending 상태)
     */
    public static function createCertification(
        int $userId,
        string $url,
        string $domain,
        string $finalGrade,
        float $finalScore,
        array $tests
    ): self {
        // 기존 pending 인증서가 있는지 확인
        $existingPending = self::where('user_id', $userId)
            ->where('url', $url)
            ->where('payment_status', 'pending')
            ->where('created_at', '>=', now()->subHours(1)) // 1시간 이내
            ->first();

        if ($existingPending) {
            return $existingPending;
        }

        // 메트릭스 구성 (테스트 ID 포함)
        $metrics = self::buildMetrics($tests);

        // 고유 코드 생성 (12자리)
        $code = self::generateUniqueCode();

        // 인증서 생성
        $certification = self::create([
            'user_id' => $userId,
            'url' => $url,
            'domain' => $domain,
            'overall_grade' => $finalGrade,
            'overall_score' => $finalScore,
            'metrics' => $metrics,
            'payment_status' => 'pending',
            'code' => $code,
            'is_valid' => false, // 결제 완료 후 true로 변경
            'is_revoked' => false,
            // issued_at, expires_at은 결제 완료 후 설정
        ]);

        // WebTest 연결은 결제 완료 후에 수행
        // 여기서는 연결하지 않음

        return $certification;
    }

    /**
     * 메트릭스 빌드
     */
    private static function buildMetrics(array $tests): array
    {
        $metrics = [
            'performance' => [],
            'security' => [],
            'quality' => [],
            'content' => [],
            'test_ids' => [], // 연관된 테스트 ID들 (결제 후 연결용)
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
     * 테스트 타입에서 카테고리 추출
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
     * 고유 코드 생성
     */
    private static function generateUniqueCode(): string
    {
        do {
            // 12자리 영숫자 대문자 코드 생성
            $code = strtoupper(Str::random(12));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * WebTest 레코드들을 인증서와 연결 (결제 완료 후 호출)
     */
    public static function linkWebTests(int $certificationId, array $testIds): void
    {
        if (!empty($testIds)) {
            WebTest::whereIn('id', $testIds)
                ->whereNull('psqc_certification_id') // 아직 연결되지 않은 것만
                ->update(['psqc_certification_id' => $certificationId]);
        }
    }

    /**
     * 결제 완료 처리
     */
    public function markAsPaid(array $paymentData = []): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_data' => $paymentData,
            'is_valid' => true,
            'issued_at' => now(),
            'expires_at' => now()->addYear(), // 1년 유효
        ]);

        // metrics에 저장된 test_ids를 사용하여 WebTest 연결
        if (isset($this->metrics['test_ids']) && !empty($this->metrics['test_ids'])) {
            self::linkWebTests($this->id, $this->metrics['test_ids']);
        }
    }

    /**
     * 결제 실패 처리
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
     * 인증서 무효화
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
        return $this->overall_score ? number_format($this->overall_score, 1) . '점' : 'N/A';
    }

    /**
     * 테스트 타입별 레이블 가져오기
     */
    public function getTestTypesAttribute(): array
    {
        return WebTest::getTestTypes();
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
}