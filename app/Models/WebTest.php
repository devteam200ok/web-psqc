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

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
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

    // Accessors
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
        return $this->overall_score ? number_format($this->overall_score, 1) . ' pts' : 'N/A';
    }

    public function getShortDomainAttribute(): string
    {
        return str_replace(['https://', 'http://', 'www.'], '', $this->domain);
    }

    // Mutators
    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
        $this->attributes['domain'] = $this->extractDomain($value);
    }

    // ===== Speed Test specific methods =====
    
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

    // ===== Legacy WebTest methods =====

    // Helper methods
    public static function getTestTypes(): array
    {
        return [
            'p-speed' => 'Global Speed',
            'p-load' => 'Load Test',
            'p-mobile' => 'Mobile Performance',
            's-ssl' => 'SSL (Basic)',
            's-sslyze' => 'SSL (Advanced)',
            's-header' => 'Security Headers',
            's-scan' => 'Vulnerability Scan',
            's-nuclei' => 'Latest Vulnerabilities',
            'q-lighthouse' => 'Overall Quality',
            'q-accessibility' => 'Accessibility (Advanced)',
            'q-compatibility' => 'Browser Compatibility',
            'q-visual' => 'Responsive UI',
            'c-links' => 'Link Validation',
            'c-structure' => 'Structured Data',
            'c-crawl' => 'Site Crawling',
            'c-meta' => 'Metadata'
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
        // Basic conditions
        if (!$this->is_certified || 
            $this->status !== 'completed' || 
            !in_array($this->overall_grade, ['A+', 'A', 'B', 'C'])) {
            return false;
        }
        
        // Cannot issue if finish time is missing
        if (!$this->finished_at) {
            return false;
        }
        
        // Must be within 3 days
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

    // Keep only the latest 100 tests per user (excluding permanent items)
    public static function cleanupOldTests(int $userId): void
    {
        // 0. Remove non-permanent items older than 30 days
        static::where('user_id', $userId)
            ->whereNull('psqc_certification_id')
            ->where('is_saved_permanently', false)
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->delete();

        // 1. Count regular (non-permanent) tests for the user
        $regularTestCount = static::where('user_id', $userId)
            ->where('is_saved_permanently', false)
            ->whereNull('psqc_certification_id')
            ->count();

        // 2. If 100 or fewer, nothing to clean
        if ($regularTestCount <= 100) {
            return;
        }

        // 3. Calculate how many to delete
        $deleteCount = $regularTestCount - 100;

        // 4. Delete the oldest tests via subquery (memory efficient)
        // In MySQL, DELETE with a subquery uses less memory
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

        // Extract the path after /storage/app/private/
        $storageRoot = storage_path('app/private') . DIRECTORY_SEPARATOR;
        if (Str::startsWith($path, $storageRoot)) {
            $rel = Str::after($path, $storageRoot);
        } else {
            // Assume it's already a relative path
            $rel = ltrim($path, '/');
        }

        // Read using the local disk (/storage/app/private is the root)
        if (!Storage::disk('local')->exists($rel)) {
            return null;
        }

        $raw = Storage::disk('local')->get($rel);

        // Memory guard (truncate if larger than 5MB)
        if (strlen($raw) > 5 * 1024 * 1024) {
            return json_encode(
                ['error' => 'JSON too large to preview'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );
        }

        $decoded = json_decode($raw, true);
        if ($decoded === null) {
            // Handle gzip-compressed data
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