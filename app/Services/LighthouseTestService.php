<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use App\Validators\UrlSecurityValidator;

class LighthouseTestService
{
    public function runTest($url, $testId)
    {
        // 보안 검증
        $securityErrors = UrlSecurityValidator::validateWithDnsCheck($url);
        if (!empty($securityErrors)) {
            throw new \Exception('보안 검증 실패: ' . implode(', ', $securityErrors));
        }

        $test = null;

        try {
            $test = WebTest::find($testId);
            if (!$test) {
                throw new \Exception('Test not found with ID: ' . $testId);
            }

            // running 표시
            $test->update(['status' => 'running']);

            // ---- 저장 경로 준비 (local 디스크: storage/app/private) ----
            // 상대키 (DB results.saved_path에 저장되는 값)
            $relativeKey = "lighthouse/{$testId}.json";
            // 절대 경로 (CLI --output-path 인자로 사용)
            $absolutePath = Storage::disk('local')->path($relativeKey);

            // 디렉토리 보장
            Storage::disk('local')->makeDirectory('lighthouse');

            $chromePath  = $this->findChromePath();
            $userDataDir = "/tmp/chrome-lighthouse-{$testId}";

            // snap일 경우 systemd-run 우회
            if (strpos($chromePath, 'snap') !== false) {
                $command = sprintf(
                    'systemd-run --user --scope lighthouse %s --output=json --output-path=%s --chrome-path=%s --chrome-flags="--headless --no-sandbox --disable-gpu --disable-dev-shm-usage --user-data-dir=%s --no-first-run"',
                    escapeshellarg($url),
                    escapeshellarg($absolutePath),
                    escapeshellarg($chromePath),
                    escapeshellarg($userDataDir)
                );
            } else {
                $command = sprintf(
                    'lighthouse %s --output=json --output-path=%s --chrome-flags="--headless --no-sandbox --disable-gpu --disable-dev-shm-usage --disable-setuid-sandbox --disable-background-timer-throttling --disable-backgrounding-occluded-windows --disable-renderer-backgrounding --user-data-dir=%s --no-first-run --disable-extensions --disable-plugins" --chrome-path=%s',
                    escapeshellarg($url),
                    escapeshellarg($absolutePath),
                    escapeshellarg($userDataDir),
                    escapeshellarg($chromePath)
                );
            }

            // 실행 (타임아웃 120초)
            $result = Process::timeout(120)->run($command);

            // 임시 Chrome 프로필 정리
            if (is_dir($userDataDir)) {
                exec('rm -rf ' . escapeshellarg($userDataDir));
            }

            if (!$result->successful()) {
                throw new \Exception('Lighthouse command failed: ' . $result->errorOutput());
            }

            // 결과 파일 확인/파싱
            if (!file_exists($absolutePath)) {
                throw new \Exception('Lighthouse output file not found');
            }

            $jsonData = json_decode(file_get_contents($absolutePath), true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($jsonData)) {
                throw new \Exception('Invalid JSON output from Lighthouse');
            }

            // 결과 저장(메트릭 계산 + DB에는 saved_path만)
            $this->parseAndSaveResults($test, $jsonData, $relativeKey);

            // (중요) 파일은 유지한다. 필요 시 다운로드/미리보기로 사용
            // unlink($absolutePath); // 삭제하지 않음

        } catch (\Exception $e) {
            Log::error('Lighthouse test failed: ' . $e->getMessage(), [
                'test_id' => $testId,
                'url'     => $url,
                'error'   => $e->getTraceAsString()
            ]);

            if ($test) {
                $test->update([
                    'status'       => 'failed',
                    'finished_at'  => now(),
                    'error_message'=> $e->getMessage()
                ]);
            }

            throw $e;
        }
    }

    private function parseAndSaveResults(WebTest $test, array $jsonData, string $relativeKey): void
    {
        $categories = $jsonData['categories'] ?? [];

        // 카테고리 점수 (PWA 제외)
        $performanceScore   = isset($categories['performance'])     ? round($categories['performance']['score'] * 100) : 0;
        $accessibilityScore = isset($categories['accessibility'])   ? round($categories['accessibility']['score'] * 100) : 0;
        $bestPracticesScore = isset($categories['best-practices'])  ? round($categories['best-practices']['score'] * 100) : 0;
        $seoScore           = isset($categories['seo'])             ? round($categories['seo']['score'] * 100) : 0;

        $avgScore = ($performanceScore + $accessibilityScore + $bestPracticesScore + $seoScore) / 4;

        // 메트릭 구성 (핵심만)
        $metrics = [
            'performance_score'     => $performanceScore,
            'accessibility_score'   => $accessibilityScore,
            'best_practices_score'  => $bestPracticesScore,
            'seo_score'             => $seoScore,
            'average_score'         => round($avgScore, 1),

            // Core Web Vitals
            'first_contentful_paint'   => $this->extractAuditValue($jsonData, 'first-contentful-paint'),
            'largest_contentful_paint' => $this->extractAuditValue($jsonData, 'largest-contentful-paint'),
            'cumulative_layout_shift'  => $this->extractAuditValue($jsonData, 'cumulative-layout-shift'),
            'speed_index'              => $this->extractAuditValue($jsonData, 'speed-index'),
            'total_blocking_time'      => $this->extractAuditValue($jsonData, 'total-blocking-time'),
            'time_to_interactive'      => $this->extractAuditValue($jsonData, 'interactive'),
        ];

        // 등급/종합점수
        $grade        = $this->calculateGrade($metrics);
        $overallScore = $avgScore;

        // DB 저장: results에는 saved_path & tested_at만 (원본 JSON은 파일로 보존)
        $test->update([
            'status'         => 'completed',
            'finished_at'    => now(),
            'overall_grade'  => $grade,
            'overall_score'  => $overallScore,
            'metrics'        => $metrics,
            'results'        => [
                'saved_path' => $relativeKey,
                'tested_at'  => now()->toISOString(),
                'audits'     => $jsonData['audits'] ?? [],           // 추가
                'categories' => $jsonData['categories'] ?? [],       // 추가
            ],
        ]);

        // 사용자별 정리
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractAuditValue($jsonData, $auditKey)
    {
        return $jsonData['audits'][$auditKey]['numericValue'] ?? null;
    }

    private function calculateGrade(array $metrics): string
    {
        $perf    = $metrics['performance_score'];
        $access  = $metrics['accessibility_score'];
        $best    = $metrics['best_practices_score'];
        $seo     = $metrics['seo_score'];
        $avg     = $metrics['average_score'];

        if ($perf >= 90 && $access >= 90 && $best >= 90 && $seo >= 90 && $avg >= 95) return 'A+';
        if ($perf >= 85 && $access >= 85 && $best >= 85 && $seo >= 85 && $avg >= 90) return 'A';
        if ($perf >= 75 && $access >= 75 && $best >= 75 && $seo >= 75 && $avg >= 80) return 'B';
        if ($perf >= 65 && $access >= 65 && $best >= 65 && $seo >= 65 && $avg >= 70) return 'C';
        if ($perf >= 55 && $access >= 55 && $best >= 55 && $seo >= 55 && $avg >= 60) return 'D';
        return 'F';
    }

    private function findChromePath()
    {
        $possible = [
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
            '/snap/bin/chromium',
        ];
        foreach ($possible as $p) {
            if (file_exists($p) && is_executable($p)) return $p;
        }
        throw new \Exception('Chrome/Chromium executable not found. Please install Google Chrome or Chromium.');
    }
}