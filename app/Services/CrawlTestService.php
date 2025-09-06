<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use App\Validators\UrlSecurityValidator;

class CrawlTestService
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
            $results = $this->performCrawlTest($url);
            
            if (!$results || !isset($results['overall'])) {
                throw new \Exception('크롤링 테스트 실행 실패');
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

    private function performCrawlTest($url): array
    {
        $cmd = [
            'node',
            base_path('scripts/content-crawl.mjs'),
            "--url={$url}",
            "--max-pages=50",
            "--timeout=15000",
        ];

        $env = [
            'HOME' => '/tmp',
            'NODE_ENV' => 'production',
        ];

        $process = new Process($cmd, base_path(), $env, null, 300);
        
        try {
            $process->mustRun();
            $json = $process->getOutput();
            $result = json_decode($json, true);
            
            if (!$result) {
                throw new \Exception('Invalid JSON output from crawler');
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Crawl test failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'stderr' => $process->getErrorOutput()
            ]);
            throw new \Exception('크롤링 실행 실패: ' . $e->getMessage());
        }
    }

    private function parseAndSaveResults($test, $data)
    {
        // 결과에서 주요 지표 추출
        $metrics = $this->extractMetrics($data);
        
        // 등급과 점수 (mjs에서 이미 계산됨)
        $grade = $data['overall']['grade'] ?? 'F';
        $score = $this->calculateScore($grade, $data);
        
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
        $robots = $data['robots'] ?? [];
        $sitemap = $data['sitemap'] ?? [];
        $pages = $data['pages'] ?? [];
        $crawlPlan = $data['crawlPlan'] ?? [];
        
        return [
            'robots' => [
                'exists' => $robots['exists'] ?? false,
                'status' => $robots['status'] ?? 0
            ],
            'sitemap' => [
                'exists' => $sitemap['hasSitemap'] ?? false,
                'url_count' => $sitemap['sitemapUrlCount'] ?? 0,
                'sitemap_count' => count($sitemap['sitemaps'] ?? [])
            ],
            'pages' => [
                'total_checked' => $pages['count'] ?? 0,
                'error_count' => $pages['errorCount4xx5xx'] ?? 0,
                'error_rate' => $pages['errorRate4xx5xx'] ?? 0,
                'quality_avg' => $pages['qualityAvg'] ?? 0,
                'duplicate_rate' => $pages['duplicateRate'] ?? 0,
                'duplicate_title_count' => $pages['dupTitleCount'] ?? 0,
                'duplicate_desc_count' => $pages['dupDescCount'] ?? 0
            ],
            'crawl_plan' => [
                'candidate_count' => $crawlPlan['candidateCount'] ?? 0,
                'skipped_count' => count($crawlPlan['skipped'] ?? [])
            ]
        ];
    }

    private function calculateScore(string $grade, array $data): float
    {
        // 기본 점수 매핑
        $baseScores = [
            'A+' => 95,
            'A' => 85,
            'B' => 75,
            'C' => 60,
            'D' => 40,
            'F' => 20
        ];
        
        $baseScore = $baseScores[$grade] ?? 0;
        
        // 세부 조정 (±5점)
        $adjustment = 0;
        $pages = $data['pages'] ?? [];
        
        // 품질 평균에 따른 조정
        $qualityAvg = $pages['qualityAvg'] ?? 0;
        if ($qualityAvg > 90) {
            $adjustment += 3;
        } elseif ($qualityAvg > 80) {
            $adjustment += 1;
        } elseif ($qualityAvg < 50) {
            $adjustment -= 3;
        }
        
        // 중복률에 따른 조정
        $duplicateRate = $pages['duplicateRate'] ?? 0;
        if ($duplicateRate < 10) {
            $adjustment += 2;
        } elseif ($duplicateRate > 50) {
            $adjustment -= 2;
        }
        
        return min(100, max(0, $baseScore + $adjustment));
    }
}