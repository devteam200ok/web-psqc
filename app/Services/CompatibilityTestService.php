<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;
use Symfony\Component\Process\Process;

class CompatibilityTestService
{
    public function runTest($url, $testId)
    {
        // 보안 검증
        $securityErrors = UrlSecurityValidator::validateWithDnsCheck($url);
        if (!empty($securityErrors)) {
            throw new \Exception('보안 검증 실패: ' . implode(', ', $securityErrors));
        }
        
        $test = WebTest::find($testId);
        
        if (!$test) {
            throw new \Exception('Test not found with ID: ' . $testId);
        }
        
        try {
            // 테스트 상태를 running으로 업데이트
            $test->update(['status' => 'running']);

            // Playwright 브라우저 호환성 테스트 실행
            $results = $this->performCompatibilityTest($url);
            
            // 결과 파싱 및 저장
            $this->parseAndSaveResults($test, $results);
            
        } catch (\Exception $e) {
            $test->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function performCompatibilityTest($url): array
    {
        $cmd = ['node', base_path('scripts/browser-compat-audit.mjs'), "--url={$url}"];
        $env = [
            'PLAYWRIGHT_BROWSERS_PATH' => '/var/www/ms-playwright',
            'HOME' => '/tmp',
        ];

        $process = new Process($cmd, base_path(), $env, null, 180);
        
        try {
            $process->mustRun();
            $json = $process->getOutput();
            $results = json_decode($json, true);
            
            if (!$results) {
                throw new \Exception('Invalid JSON output from browser compatibility test');
            }
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Browser compatibility test failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'stderr' => $process->getErrorOutput()
            ]);
            throw new \Exception('브라우저 호환성 테스트 실행 실패: ' . $e->getMessage());
        }
    }

    private function parseAndSaveResults($test, $data)
    {
        // mjs에서 이미 등급이 계산되어 있음
        $grade = $data['overall']['grade'] ?? 'F';
        $score = $this->calculateScore($grade);
        
        // 메트릭 추출
        $metrics = $this->extractMetrics($data);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => [
                'report' => $data,
                'tested_at' => now()->toISOString(),
            ],
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리 (로그인 사용자인 경우)
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics(array $data): array
    {
        $totals = $data['totals'] ?? [];
        $perBrowser = $data['perBrowser'] ?? [];
        
        $metrics = [
            'summary' => [
                'ok_count' => $totals['okCount'] ?? 0,
                'js_first_party_total' => $totals['jsFirstPartyTotal'] ?? 0,
                'js_third_party_total' => $totals['jsThirdPartyTotal'] ?? 0,
                'js_noise_total' => $totals['jsNoiseTotal'] ?? 0,
                'css_total' => $totals['cssTotal'] ?? 0,
                'grade' => $data['overall']['grade'] ?? 'F',
                'reason' => $data['overall']['reason'] ?? '',
                'strict_mode' => $data['strictMode'] ?? false,
            ],
            'browsers' => []
        ];
        
        // 브라우저별 상세 메트릭
        foreach ($perBrowser as $browser) {
            $browserName = $browser['browser'] ?? '';
            $metrics['browsers'][$browserName] = [
                'ok' => $browser['ok'] ?? false,
                'js_first_party' => $browser['jsFirstPartyCount'] ?? 0,
                'js_third_party' => $browser['jsThirdPartyCount'] ?? 0,
                'js_noise' => $browser['jsNoiseCount'] ?? 0,
                'css_errors' => $browser['cssErrorCount'] ?? 0,
                'nav_error' => $browser['navError'] ?? null,
                'user_agent' => $browser['userAgent'] ?? '',
                'samples' => $browser['samples'] ?? []
            ];
        }
        
        return $metrics;
    }

    private function calculateScore(string $grade): float
    {
        // A+ 등급은 완벽한 상태이므로 100점
        // 다른 등급은 범위 내 중간값 사용
        return match($grade) {
            'A+' => 100.0,  // 완벽한 호환성
            'A' => 85.0,    // 매우 우수
            'B' => 75.0,    // 우수
            'C' => 65.0,    // 보통
            'D' => 55.0,    // 미흡
            default => 25.0 // F - 심각
        };
    }
}