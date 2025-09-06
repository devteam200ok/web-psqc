<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use App\Validators\UrlSecurityValidator;

class MobileTestService
{
    private function testConnection(string $url): bool
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'HEAD',
                    'timeout' => 10,
                    'ignore_errors' => true
                ]
            ]);
            
            $headers = @get_headers($url, 1, $context);
            return $headers !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function runTest($url, $testId)
    {
        // 보안 검증
        $securityErrors = UrlSecurityValidator::validateWithDnsCheck($url);
        if (!empty($securityErrors)) {
            throw new \Exception('보안 검증 실패: ' . implode(', ', $securityErrors));
        }
        
        // 연결 테스트
        if (!$this->testConnection($url)) {
            throw new \Exception('대상 URL에 연결할 수 없습니다.');
        }
        
        $test = null;
        
        try {
            // WebTest 찾기
            $test = WebTest::find($testId);
            
            if (!$test) {
                throw new \Exception('Test not found with ID: ' . $testId);
            }
            
            // 테스트 상태를 running으로 업데이트
            $test->update(['status' => 'running']);

            $results = $this->performMobileTest($url);
            
            if (empty($results)) {
                throw new \Exception('Mobile test failed to produce results');
            }
            
            // 결과 파싱 및 저장
            $this->parseAndSaveResults($test, $results);
            
        } catch (\Exception $e) {
            // $test가 존재할 때만 업데이트
            if ($test) {
                $test->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'error_message' => $e->getMessage()
                ]);
            }
            
            Log::error('Mobile test failed: ' . $e->getMessage(), [
                'test_id' => $testId,
                'url' => $url,
                'error' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    private function performMobileTest($url): array
    {
        $cmd = ['node', base_path('scripts/mobile-audit.mjs'), "--url={$url}"];

        $env = [
            // Playwright 브라우저 바이너리 경로
            'PLAYWRIGHT_BROWSERS_PATH' => '/var/www/ms-playwright',
        ];

        $process = new Process($cmd, base_path(), $env, null, 180);
        
        try {
            $process->mustRun();
            $json = $process->getOutput();
            $results = json_decode($json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON output from mobile audit script');
            }
            
            return $results;
        } catch (\Exception $e) {
            throw new \Exception('Mobile audit failed: ' . $e->getMessage() . PHP_EOL . $process->getErrorOutput());
        }
    }

    private function parseAndSaveResults($test, $rawResults)
    {
        // 메트릭 추출
        $metrics = $this->extractMetrics($rawResults);
        
        // 등급 계산
        $grade = $this->calculateGrade($metrics);
        $score = $this->calculateScore($grade);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => $rawResults,
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리 (로그인 사용자인 경우)
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics(array $rawResults): array
    {
        $metrics = [];
        
        $overall = $rawResults['overall'] ?? [];
        $results = $rawResults['results'] ?? [];
        
        // 전체 평균 메트릭
        $metrics['median_avg_ms'] = $overall['medianAvgMs'] ?? 0;
        $metrics['long_tasks_avg_ms'] = $overall['longTasksAvgMs'] ?? 0;
        $metrics['js_errors_first_party_total'] = $overall['jsErrorsFirstPartyTotal'] ?? 0;
        $metrics['js_errors_third_party_total'] = $overall['jsErrorsThirdPartyTotal'] ?? 0;
        $metrics['body_overflows_viewport'] = !empty($overall['bodyOverflowsViewport']);
        $metrics['grade'] = $overall['grade'] ?? 'F';
        $metrics['reason'] = $overall['reason'] ?? '';
        
        // 디바이스별 상세 메트릭
        $deviceMetrics = [];
        foreach ($results as $result) {
            $device = $result['device'] ?? 'unknown';
            $deviceMetrics[$device] = [
                'runs' => $result['runs'] ?? [],
                'valid_runs' => $result['validRuns'] ?? [],
                'median_ms' => $result['medianMs'] ?? 0,
                'long_tasks_total_ms' => $result['longTasksTotalMs'] ?? 0,
                'js_errors_first_party_count' => $result['jsErrorsFirstPartyCount'] ?? 0,
                'js_errors_third_party_count' => $result['jsErrorsThirdPartyCount'] ?? 0,
                'js_errors_unique_count' => $result['jsErrorsUniqueCount'] ?? 0,
                'js_errors_by_origin' => $result['jsErrorsByOrigin'] ?? [],
                'body_overflows_viewport' => !empty($result['bodyOverflowsViewport']),
                'viewport' => $result['viewport'] ?? null,
                'user_agent' => $result['userAgent'] ?? '',
            ];
        }
        $metrics['devices'] = $deviceMetrics;
        
        return $metrics;
    }

    private function calculateGrade(array $metrics): string
    {
        $medianAvgMs = $metrics['median_avg_ms'] ?? 0;
        $jsErrorsFirstParty = $metrics['js_errors_first_party_total'] ?? 0;
        $bodyOverflows = $metrics['body_overflows_viewport'] ?? false;
        
        // A+ 조건: ≤800ms, 자사 JS 에러 0, 렌더 폭 정상
        if ($medianAvgMs <= 800 && $jsErrorsFirstParty === 0 && !$bodyOverflows) {
            return 'A+';
        }
        
        // A 조건: ≤1200ms, 자사 JS 에러 ≤1, 렌더 폭 정상
        if ($medianAvgMs <= 1200 && $jsErrorsFirstParty <= 1 && !$bodyOverflows) {
            return 'A';
        }
        
        // B 조건: ≤2000ms, 자사 JS 에러 ≤2
        if ($medianAvgMs <= 2000 && $jsErrorsFirstParty <= 2) {
            return 'B';
        }
        
        // C 조건: ≤3000ms, 자사 JS 에러 ≤3
        if ($medianAvgMs <= 3000 && $jsErrorsFirstParty <= 3) {
            return 'C';
        }
        
        // D 조건: ≤4000ms, 자사 JS 에러 ≤5
        if ($medianAvgMs <= 4000 && $jsErrorsFirstParty <= 5) {
            return 'D';
        }
        
        return 'F';
    }

    private function calculateScore(string $grade): float
    {
        return match($grade) {
            'A+' => rand(90, 100),
            'A' => rand(80, 89),
            'B' => rand(70, 79),
            'C' => rand(60, 69),
            'D' => rand(50, 59),
            default => rand(0, 49)
        };
    }
}