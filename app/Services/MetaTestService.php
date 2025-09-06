<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use App\Validators\UrlSecurityValidator;

class MetaTestService
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
            
            // 테스트 상태를 running으로 업데이트
            $test->update(['status' => 'running']);

            // Node.js 스크립트 실행
            $results = $this->performMetaTest($url);
            
            if (isset($results['error']) && $results['error']) {
                throw new \Exception($results['error']);
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

    private function performMetaTest($url): array
    {
        $cmd = [
            'node',
            base_path('scripts/content-meta.mjs'),
            "--url={$url}",
            "--timeout=15000",
        ];

        $env = [
            'HOME' => '/tmp',
            'NODE_ENV' => 'production',
        ];

        $process = new Process($cmd, base_path(), $env, null, 60);
        
        try {
            $process->mustRun();
            $json = $process->getOutput();
            $result = json_decode($json, true);
            
            if (!$result) {
                throw new \Exception('Invalid JSON response from meta test script');
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Meta test script execution failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'stderr' => $process->getErrorOutput()
            ]);
            
            throw new \Exception('메타데이터 검사 실행 실패: ' . $e->getMessage());
        }
    }

    private function parseAndSaveResults($test, $data)
    {
        // mjs에서 이미 계산된 점수를 100점 만점으로 변환 (20점 만점 -> 100점 만점)
        $originalScore = $data['score'] ?? 0;
        $score = ($originalScore / 20) * 100;
        
        // mjs에서 이미 계산된 등급 사용
        $grade = $data['grade']['grade'] ?? 'F';
        
        // 메트릭 추출
        $metrics = $this->extractMetrics($data);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => $data, // 전체 결과 저장
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리 (로그인 사용자인 경우)
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics(array $data): array
    {
        $metadata = $data['metadata'] ?? [];
        $analysis = $data['analysis'] ?? [];
        $summary = $data['summary'] ?? [];
        
        return [
            'title' => [
                'content' => $metadata['title'] ?? '',
                'length' => $summary['titleLength'] ?? 0,
                'is_optimal' => $analysis['title']['isOptimal'] ?? false,
                'is_acceptable' => $analysis['title']['isAcceptable'] ?? false,
            ],
            'description' => [
                'content' => $metadata['description'] ?? '',
                'length' => $summary['descriptionLength'] ?? 0,
                'is_optimal' => $analysis['description']['isOptimal'] ?? false,
                'is_acceptable' => $analysis['description']['isAcceptable'] ?? false,
            ],
            'canonical' => [
                'url' => $metadata['canonical'] ?? null,
                'exists' => $summary['hasCanonical'] ?? false,
                'is_correct' => $analysis['canonical']['isCorrect'] ?? false,
            ],
            'open_graph' => [
                'count' => $summary['openGraphFields'] ?? 0,
                'has_basic' => $analysis['openGraph']['hasBasic'] ?? false,
                'is_perfect' => $analysis['openGraph']['isPerfect'] ?? false,
                'data' => $metadata['openGraph'] ?? [],
            ],
            'twitter_cards' => [
                'count' => $summary['twitterCardFields'] ?? 0,
                'has_basic' => $analysis['twitterCards']['hasBasic'] ?? false,
                'is_perfect' => $analysis['twitterCards']['isPerfect'] ?? false,
                'data' => $metadata['twitterCards'] ?? [],
            ],
            'hreflang' => [
                'count' => $summary['hreflangCount'] ?? 0,
                'has_x_default' => $analysis['hreflang']['hasXDefault'] ?? false,
                'links' => $metadata['hreflangs'] ?? [],
            ],
        ];
    }
}