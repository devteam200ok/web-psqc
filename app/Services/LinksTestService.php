<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;
use Symfony\Component\Process\Process;

class LinksTestService
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

            // Node.js 스크립트 실행
            $results = $this->performLinksTest($url);
            
            if (empty($results)) {
                throw new \Exception('링크 검증 결과를 받지 못했습니다.');
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
            throw $e;
        }
    }

    private function performLinksTest($url): array
    {
        $cmd = [
            'node',
            base_path('scripts/content-links-audit.mjs'),
            "--url={$url}",
        ];

        $env = [
            'PLAYWRIGHT_BROWSERS_PATH' => env('PLAYWRIGHT_BROWSERS_PATH', '/var/www/ms-playwright'),
            'HOME' => '/tmp', // 권한/프로필 이슈 회피
        ];

        $process = new Process($cmd, base_path(), $env, null, 240);
        
        try {
            $process->mustRun();
            $output = $process->getOutput();
            
            $result = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from links audit: ' . json_last_error_msg());
            }
            
            return $result;
            
        } catch (\Exception $e) {
            $errorOutput = $process->getErrorOutput();
            Log::error('Links test failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'stderr' => $errorOutput
            ]);
            throw new \Exception('링크 검증 실행 실패: ' . $e->getMessage());
        }
    }

    private function parseAndSaveResults($test, array $data)
    {
        // 메트릭 추출
        $metrics = $this->extractMetrics($data);
        
        // 등급은 mjs에서 이미 계산됨
        $grade = $data['overall']['grade'] ?? 'F';
        
        // 점수 계산 (100점 만점)
        $score = $this->calculateScore($grade, $data);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => $data, // 전체 결과를 그대로 저장
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
        $rates = $data['rates'] ?? [];
        $overall = $data['overall'] ?? [];
        
        return [
            // 검사 대상 수
            'checked' => [
                'http' => $totals['httpChecked'] ?? 0,
                'internal' => $totals['internalChecked'] ?? 0,
                'external' => $totals['externalChecked'] ?? 0,
                'image' => $totals['imageChecked'] ?? 0,
                'anchor' => $totals['anchorChecked'] ?? 0,
            ],
            // 오류 수
            'errors' => [
                'http' => $totals['httpErrors'] ?? 0,
                'internal' => $totals['internalErrors'] ?? 0,
                'external' => $totals['externalErrors'] ?? 0,
                'image' => $totals['imageErrors'] ?? 0,
                'anchor' => $totals['anchorErrors'] ?? 0,
            ],
            // 오류율 (%)
            'error_rates' => [
                'overall' => $rates['overallErrorRate'] ?? 0,
                'internal' => $rates['internalErrorRate'] ?? 0,
                'external' => $rates['externalErrorRate'] ?? 0,
                'image' => $rates['imageErrorRate'] ?? 0,
                'anchor' => $rates['anchorErrorRate'] ?? 0,
            ],
            // 리다이렉트 정보
            'redirect' => [
                'max_chain' => $totals['maxRedirectChain'] ?? 0,
                'max_chain_effective' => $totals['maxRedirectChainEffective'] ?? 0,
                'redirected_count' => $totals['redirectedCount'] ?? 0,
                'redirected_count_effective' => $totals['redirectedCountEffective'] ?? 0,
                'has_redirect_loop' => $totals['hasRedirectLoop'] ?? false,
            ],
            // 등급 정보
            'grade_info' => [
                'grade' => $overall['grade'] ?? 'F',
                'reason' => $overall['reason'] ?? '',
            ],
            // 네비게이션 오류
            'nav_error' => $totals['navError'] ?? null,
        ];
    }

    private function calculateScore(string $grade, array $data): float
    {
        $rates = $data['rates'] ?? [];
        $totals = $data['totals'] ?? [];
        
        // 기본 점수 (등급 기반)
        $baseScore = match($grade) {
            'A+' => 95,
            'A' => 85,
            'B' => 75,
            'C' => 65,
            'D' => 55,
            default => 45,
        };
        
        // 세부 조정 (±5점 범위)
        $adjustment = 0;
        
        // 오류율 기반 조정
        $overallErrorRate = $rates['overallErrorRate'] ?? 0;
        if ($overallErrorRate == 0) {
            $adjustment += 2.5;
        } else if ($overallErrorRate < 1) {
            $adjustment += 1.5;
        } else if ($overallErrorRate > 5) {
            $adjustment -= 2;
        }
        
        // 리다이렉트 체인 기반 조정
        $maxChain = $totals['maxRedirectChainEffective'] ?? 0;
        if ($maxChain <= 1) {
            $adjustment += 1.5;
        } else if ($maxChain > 3) {
            $adjustment -= 1.5;
        }
        
        // 앵커 오류율 기반 조정
        $anchorErrorRate = $rates['anchorErrorRate'] ?? 0;
        if ($anchorErrorRate == 0) {
            $adjustment += 1;
        } else if ($anchorErrorRate > 10) {
            $adjustment -= 1.5;
        }
        
        // 최종 점수 계산 (0-100 범위로 제한)
        $finalScore = max(0, min(100, $baseScore + $adjustment));
        
        return round($finalScore, 1);
    }
}