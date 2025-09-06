<?php

namespace App\Services;

use App\Models\WebTest;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;

class VisualTestService
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
            $test->update(['status' => 'running']);

            // Node.js 스크립트 실행
            $results = $this->performVisualTest($url);
            
            if (!$results) {
                throw new \Exception('Visual test script failed to return results');
            }
            
            // 결과 파싱 및 저장
            $this->parseAndSaveResults($test, $results);
            
        } catch (\Exception $e) {
            if ($test) {
                $test->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'error_message' => $e->getMessage()
                ]);
            }
            throw $e;
        }
    }

    private function performVisualTest($url): array
    {
        $cmd = [
            'node',
            base_path('scripts/ui-visual-audit.mjs'),
            "--url={$url}",
        ];

        $env = [
            'PLAYWRIGHT_BROWSERS_PATH' => '/var/www/ms-playwright',
            'HOME' => '/tmp',
        ];

        $process = new Process($cmd, base_path(), $env, null, 240);
        
        try {
            $process->mustRun();
            $output = $process->getOutput();
            $data = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error('Visual test process failed', [
                'error' => $e->getMessage(),
                'stderr' => $process->getErrorOutput()
            ]);
            throw new \Exception('Visual test failed: ' . $e->getMessage());
        }
    }

    private function parseAndSaveResults($test, $data)
    {
        // mjs가 이미 등급을 계산해서 제공
        $grade = $data['overall']['grade'] ?? 'F';
        $reason = $data['overall']['reason'] ?? '';
        
        // 점수 계산 (100점 만점)
        $score = $this->calculateScore($grade, $data);
        
        // 메트릭 추출
        $metrics = $this->extractMetrics($data);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => [
                'url' => $data['url'] ?? '',
                'set' => $data['set'] ?? 'all',
                'testedAt' => $data['testedAt'] ?? now()->toISOString(),
                'perViewport' => $data['perViewport'] ?? [],
                'totals' => $data['totals'] ?? [],
                'overall' => $data['overall'] ?? [],
                'criteria' => $data['criteria'] ?? []
            ],
            'metrics' => $metrics
        ]);

        // 사용자별 테스트 정리
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics(array $data): array
    {
        $totals = $data['totals'] ?? [];
        $perViewport = $data['perViewport'] ?? [];
        
        // 뷰포트별 상세 메트릭
        $viewportMetrics = [];
        foreach ($perViewport as $vp) {
            $viewportMetrics[$vp['viewport']] = [
                'overflow' => $vp['overflow'] ?? false,
                'overflowPx' => $vp['overflowPx'] ?? 0,
                'viewportWidth' => $vp['viewportWidth'] ?? $vp['w'],
                'bodyRenderWidth' => $vp['bodyRenderWidth'] ?? 0,
                'hasError' => !empty($vp['navError'])
            ];
        }
        
        return [
            'overflowCount' => $totals['overflowCount'] ?? 0,
            'maxOverflowPx' => $totals['maxOverflowPx'] ?? 0,
            'viewportDetails' => $viewportMetrics,
            'totalViewports' => count($perViewport),
            'failedViewports' => count(array_filter($perViewport, fn($v) => !empty($v['navError'])))
        ];
    }

    private function calculateScore(string $grade, array $data): float
    {
        $overflowCount = $data['totals']['overflowCount'] ?? 0;
        $maxOverflowPx = $data['totals']['maxOverflowPx'] ?? 0;
        
        // 기본 점수 (등급별)
        $baseScore = match($grade) {
            'A+' => 100,
            'A' => 90,
            'B' => 80,
            'C' => 70,
            'D' => 60,
            'F' => 50,
            default => 40
        };
        
        // 세부 조정
        if ($grade === 'A+') {
            return 100; // 완벽
        }
        
        if ($grade === 'A') {
            // 초과가 적을수록 높은 점수 (90-95)
            return min(95, $baseScore + max(0, 5 - $maxOverflowPx));
        }
        
        if ($grade === 'B') {
            // 초과 정도에 따라 조정 (80-89)
            $penalty = min(9, $overflowCount * 2 + floor($maxOverflowPx / 4));
            return max(80, 89 - $penalty);
        }
        
        if ($grade === 'C') {
            // 초과 정도에 따라 조정 (70-79)
            $penalty = min(9, $overflowCount + floor($maxOverflowPx / 8));
            return max(70, 79 - $penalty);
        }
        
        if ($grade === 'D') {
            // 초과 정도에 따라 조정 (50-69)
            $penalty = min(19, floor($overflowCount / 2) + floor($maxOverflowPx / 16));
            return max(50, 69 - $penalty);
        }
        
        // F등급
        if ($maxOverflowPx >= 100) {
            return 0; // 매우 심각
        }
        return max(0, 49 - floor($maxOverflowPx / 10));
    }
}