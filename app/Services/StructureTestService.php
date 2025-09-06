<?php

namespace App\Services;

use App\Models\WebTest;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;

class StructureTestService
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
            // WebTest 찾기
            $test = WebTest::find($testId);
            
            if (!$test) {
                throw new \Exception('Test not found with ID: ' . $testId);
            }
            
            // 테스트 상태를 running으로 업데이트
            $test->update(['status' => 'running']);

            // 구조화 데이터 테스트 실행
            $results = $this->performStructureTest($url);
            
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

    private function performStructureTest($url): array
    {
        $cmd = [
            'node',
            base_path('scripts/content-structure-audit.mjs'),
            "--url={$url}",
        ];

        $env = [
            'PLAYWRIGHT_BROWSERS_PATH' => '/var/www/ms-playwright',
            'HOME' => '/tmp',
        ];

        $process = new Process($cmd, base_path(), $env, null, 180);
        
        try {
            $process->mustRun();
            $json = $process->getOutput();
            $result = json_decode($json, true);
            
            if (!$result) {
                throw new \Exception('Invalid JSON response from structure audit script');
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Structure test failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'stderr' => $process->getErrorOutput()
            ]);
            throw new \Exception('Structure test failed: ' . $e->getMessage());
        }
    }

    private function parseAndSaveResults($test, $data)
    {
        // 메트릭 추출
        $metrics = $this->extractMetrics($data);
        
        // mjs에서 이미 계산된 등급 사용
        $grade = $data['overall']['grade'] ?? 'F';
        $score = $this->calculateScore($grade);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => $data,
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
        $types = $data['types'] ?? [];
        
        return [
            'json_ld_blocks' => $totals['jsonLdBlocks'] ?? 0,
            'json_ld_items' => $totals['jsonLdItems'] ?? 0,
            'parse_errors' => $totals['parseErrors'] ?? 0,
            'item_errors' => $totals['itemErrors'] ?? 0,
            'item_warnings' => $totals['itemWarnings'] ?? 0,
            'has_microdata' => $totals['hasMicrodata'] ?? false,
            'has_rdfa' => $totals['hasRdfa'] ?? false,
            'rich_eligible_types' => $totals['richEligibleTypes'] ?? [],
            'schema_types' => array_slice($types, 0, 10), // 상위 10개 타입만 저장
            'total_errors' => ($totals['parseErrors'] ?? 0) + ($totals['itemErrors'] ?? 0),
            'total_warnings' => $totals['itemWarnings'] ?? 0,
        ];
    }

    private function calculateScore(string $grade): float
    {
        // 등급에 따른 점수 범위 설정
        return match($grade) {
            'A+' => rand(90, 100) / 1.0,
            'A' => rand(80, 89) / 1.0,
            'B' => rand(70, 79) / 1.0,
            'C' => rand(60, 69) / 1.0,
            'D' => rand(50, 59) / 1.0,
            default => rand(0, 49) / 1.0
        };
    }
}