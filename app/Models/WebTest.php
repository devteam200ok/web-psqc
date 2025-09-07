<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class WebTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_type',
        'url',
        'domain',
        'status',
        'started_at',
        'finished_at',
        'test_config',
        'overall_grade',
        'overall_score',
        'metrics',
        'results',
        'error_message',
        'is_certified',
        'is_saved_permanently',
        'psqc_certification_id',
    ];

    protected $casts = [
        'test_config' => 'array',
        'metrics' => 'array',
        'results' => 'array',
        'overall_score' => 'decimal:2',
        'is_certified' => 'boolean',
        'is_saved_permanently' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // 관계 설정
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 스코프
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByTestType($query, string $testType)
    {
        return $query->where('test_type', $testType);
    }

    public function scopeByDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    public function scopeCertifiable($query)
    {
        return $query->where('is_certified', true);
    }

    public function scopePermanentlySaved($query)
    {
        return $query->where('is_saved_permanently', true);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByGrade($query, array $grades)
    {
        return $query->whereIn('overall_grade', $grades);
    }

    public function scopeLight($query)
    {
        return $query->select([
            'id',
            'user_id',
            'psqc_certification_id',
            'test_type',
            'domain',
            'url',
            'status',
            'overall_grade',
            'overall_score',
            'is_saved_permanently',
            'created_at',
            'finished_at'
        ]);
    }

    // 접근자
    public function getTestDurationAttribute(): ?int
    {
        if (!$this->started_at || !$this->finished_at) {
            return null;
        }
        
        return $this->finished_at->diffInSeconds($this->started_at);
    }

    public function getIsRunningAttribute(): bool
    {
        return in_array($this->status, ['pending', 'queued', 'running']);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
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

    public function getShortDomainAttribute(): string
    {
        return str_replace(['https://', 'http://', 'www.'], '', $this->domain);
    }

    // 수정자
    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
        $this->attributes['domain'] = $this->extractDomain($value);
    }

    // ===== Speed Test 전용 메서드들 =====
    
    /**
     * Get status badge class for Livewire component
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'completed' => 'bg-blue-lt text-blue-lt-fg',
            'failed' => 'bg-red-lt text-red-lt-fg',
            'running' => 'bg-teal-lt text-teal-lt-fg',
            default => 'bg-azure-lt text-azure-lt-fg'
        };
    }

    /**
     * Get metric for a specific region, metric type, and visit type
     * 
     * @param string $region (seoul, tokyo, sydney, london, frankfurt, virginia, oregon, singapore)
     * @param string $metric (ttfb, load, bytes, resources) 
     * @param string $visitType (first, repeat)
     * @return mixed
     */
    public function getMetric(string $region, string $metric, string $visitType = 'first')
    {
        return data_get($this->metrics, "{$region}.{$visitType}.{$metric}");
    }

    /**
     * Get all metrics for a specific region
     * 
     * @param string $region
     * @return array|null
     */
    public function getRegionMetrics(string $region): ?array
    {
        return data_get($this->metrics, $region);
    }

    /**
     * Get average TTFB across all regions
     * 
     * @param string $visitType
     * @return float|null
     */
    public function getAverageTTFB(string $visitType = 'first'): ?float
    {
        if (!$this->metrics) return null;
        
        $ttfbs = [];
        foreach ($this->metrics as $region => $data) {
            if (isset($data[$visitType]['ttfb'])) {
                $ttfbs[] = $data[$visitType]['ttfb'];
            }
        }
        
        return empty($ttfbs) ? null : array_sum($ttfbs) / count($ttfbs);
    }

    /**
     * Get fastest region for a specific metric
     * 
     * @param string $metric
     * @param string $visitType
     * @return array|null ['region' => string, 'value' => float]
     */
    public function getFastestRegion(string $metric = 'ttfb', string $visitType = 'first'): ?array
    {
        if (!$this->metrics) return null;
        
        $best = null;
        foreach ($this->metrics as $region => $data) {
            $value = data_get($data, "{$visitType}.{$metric}");
            if ($value !== null && ($best === null || $value < $best['value'])) {
                $best = ['region' => $region, 'value' => $value];
            }
        }
        
        return $best;
    }

    // ===== 기존 WebTest 메서드들 =====

    // 헬퍼 메서드
    public static function getTestTypes(): array
    {
        return [
            'p-speed' => '글로벌 속도', 
            'p-load' => '부하 테스트',
            'p-mobile' => '모바일 성능',
            's-ssl' => 'SSL 기본',
            's-sslyze' => 'SSL 심화',
            's-header' => '보안 헤더',
            's-scan' => '취약점 스캔',
            's-nuclei' => '최신 취약점',
            'q-lighthouse' => '종합 품질',
            'q-accessibility' => '접근성 심화',
            'q-compatibility' => '브라우저 호환',
            'q-visual' => '반응형 UI',
            'c-links' => '링크 검증',
            'c-structure' => '구조화 데이터',
            'c-crawl' => '사이트 크롤링',
            'c-meta' => '메타데이터'
        ];
    }

    public static function getGrades(): array
    {
        return ['A+', 'A', 'B', 'C', 'D', 'F'];
    }

    public static function getStatuses(): array
    {
        return ['pending', 'queued', 'running', 'completed', 'failed'];
    }

    public function canIssueCertificate(): bool
    {
        // 기본 조건 확인
        if (!$this->is_certified || 
            $this->status !== 'completed' || 
            !in_array($this->overall_grade, ['A+', 'A', 'B', 'C'])) {
            return false;
        }
        
        // 완료 시간이 없으면 발급 불가
        if (!$this->finished_at) {
            return false;
        }
        
        // 3일 이내인지 확인
        return $this->finished_at->diffInDays(now()) <= 3;
    }

    public function isExpiredForCertificate(): bool
    {
        if (!$this->finished_at) {
            return true;
        }
        
        return $this->finished_at->addDays(3)->isPast();
    }

    public function setMetric(string $key, $value): void
    {
        $metrics = $this->metrics ?? [];
        data_set($metrics, $key, $value);
        $this->metrics = $metrics;
    }

    private function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }

    // 사용자별 최근 100개 제한을 위한 메서드 (permanent 제외)
    public static function cleanupOldTests(int $userId): void
    {
        // 0. 30일 경과한 비영구 저장 항목 정리
        static::where('user_id', $userId)
            ->whereNull('psqc_certification_id')
            ->where('is_saved_permanently', false)
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->delete();

        // 1. 해당 사용자의 regular 테스트 개수 확인
        $regularTestCount = static::where('user_id', $userId)
            ->where('is_saved_permanently', false)
            ->whereNull('psqc_certification_id')
            ->count();

        // 2. 100개 이하면 정리할 필요 없음
        if ($regularTestCount <= 100) {
            return;
        }

        // 3. 삭제할 개수 계산
        $deleteCount = $regularTestCount - 100;

        // 4. 가장 오래된 테스트들을 서브쿼리로 삭제 (메모리 효율적)
        // MySQL에서 서브쿼리를 사용한 삭제는 메모리를 덜 사용함
        \DB::statement("
            DELETE FROM web_tests 
            WHERE user_id = ? 
            AND is_saved_permanently = 0 
            AND psqc_certification_id IS NULL
            AND id IN (
                SELECT id FROM (
                    SELECT id 
                    FROM web_tests 
                    WHERE user_id = ? 
                        AND is_saved_permanently = 0 
                        AND psqc_certification_id IS NULL
                    ORDER BY created_at ASC 
                    LIMIT ?
                ) as temp_table
            )
        ", [$userId, $userId, $deleteCount]);
    }

    public function getRawJsonPrettyAttribute(): ?string
    {
        $path = data_get($this->results, 'saved_path');
        if (!$path) return null;

        // /storage/app/private/ 이후의 경로만 추출
        $storageRoot = storage_path('app/private') . DIRECTORY_SEPARATOR;
        if (Str::startsWith($path, $storageRoot)) {
            $rel = Str::after($path, $storageRoot);
        } else {
            // 이미 상대경로라고 가정
            $rel = ltrim($path, '/');
        }

        // 이제 local 디스크 기준으로 조회 (/storage/app/private 가 root)
        if (!Storage::disk('local')->exists($rel)) {
            return null;
        }

        $raw = Storage::disk('local')->get($rel);

        // 메모리 보호 (5MB 이상이면 잘라내기)
        if (strlen($raw) > 5 * 1024 * 1024) {
            return json_encode(
                ['error' => 'JSON too large to preview'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );
        }

        $decoded = json_decode($raw, true);
        if ($decoded === null) {
            // gzip 압축 대응
            $maybe = @gzdecode($raw);
            if ($maybe !== false) {
                $decoded = json_decode($maybe, true);
            }
        }

        if ($decoded === null) return null;

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function psqcCertification()
    {
        return $this->belongsTo(PsqcCertification::class, 'psqc_certification_id');
    }
}
