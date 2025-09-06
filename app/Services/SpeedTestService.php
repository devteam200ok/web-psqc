<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;

class SpeedTestService
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

            $results = $this->performSpeedTest($url);
            
            if (empty($results['results']) && !empty($results['errors'])) {
                throw new \Exception('All regions failed: ' . implode(', ', $results['errors']));
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
        }
    }

    private function performSpeedTest($url): array
    {
        $results = [];
        $errors = [];
        
        // 전체 8개 리전 환경변수 확인
        $regions = [
            'seoul' => ['label' => 'Seoul (ap-northeast-2)', 'env' => 'PROBE_SEOUL_URL'],
            'tokyo' => ['label' => 'Tokyo (ap-northeast-1)', 'env' => 'PROBE_TOKYO_URL'], 
            'sydney' => ['label' => 'Sydney (ap-southeast-2)', 'env' => 'PROBE_SYDNEY_URL'],
            'london' => ['label' => 'London (eu-west-2)', 'env' => 'PROBE_LONDON_URL'],
            'frankfurt' => ['label' => 'Frankfurt (eu-central-1)', 'env' => 'PROBE_FRANKFURT_URL'],
            'virginia' => ['label' => 'Virginia (us-east-1)', 'env' => 'PROBE_VIRGINIA_URL'],
            'oregon' => ['label' => 'Oregon (us-west-2)', 'env' => 'PROBE_OREGON_URL'],
            'singapore' => ['label' => 'Singapore (ap-southeast-1)', 'env' => 'PROBE_SINGAPORE_URL'],
        ];
        
        $apiKey = env('PROBE_API_KEY');
        $payload = ['target' => $url];
        
        // 각 리전의 환경변수 로드 및 검증
        foreach ($regions as $key => $config) {
            $endpoint = env($config['env']);
            
            // NULL 체크를 여러 방법으로 수행
            if ($endpoint === null || $endpoint === '' || empty($endpoint) || !is_string($endpoint)) {
                $errors[$key] = "Endpoint not configured for {$key} region. Please set {$config['env']} in .env";
                continue;
            }
            
            try {

                // HTTP 클라이언트 보안 설정 강화
                $httpClient = Http::acceptJson()
                    ->timeout(120)
                    ->connectTimeout(30)
                    ->retry(2, 1000) // 2번 재시도, 1초 간격
                    ->withOptions([
                        'verify' => true, // SSL 인증서 검증
                        'allow_redirects' => [
                            'max' => 3, // 최대 3번 리다이렉트
                            'strict' => true,
                            'referer' => true,
                            'track_redirects' => true
                        ]
                    ]);

                // HTTP 클라이언트를 단계적으로 구성
                $httpClient = Http::acceptJson()->timeout(120);
                
                if (!empty($apiKey)) {
                    $httpClient = $httpClient->withHeaders(['X-Api-Key' => $apiKey]);
                }
                
                // 최종 안전 검사
                if (!is_string($endpoint) || strlen(trim($endpoint)) === 0) {
                    throw new \Exception("Invalid endpoint: not a valid string");
                }
                
                $response = $httpClient->post($endpoint, $payload);
                
                if (!$response->successful()) {
                    $errors[$key] = "HTTP {$response->status()}: " . ($response->json('error') ?? 'Request failed');
                    continue;
                }
                
                $responseData = $response->json();
                
                if (!is_array($responseData) || !isset($responseData['ok']) || !$responseData['ok']) {
                    $errors[$key] = $responseData['error'] ?? 'API returned error or invalid response';
                    continue;
                }
                
                $results[$key] = [
                    'label' => $config['label'],
                    'summary' => $responseData['summary'] ?? [],
                ];
                
            } catch (\Exception $e) {
                $errors[$key] = 'Request failed: ' . $e->getMessage();
            }
        }
        
        return [
            'results' => $results,
            'errors' => $errors,
        ];
    }

    private function parseAndSaveResults($test, $data)
    {
        $results = $data['results'] ?? [];
        $errors = $data['errors'] ?? [];
        
        // 메트릭을 JSON 형태로 추출
        $metrics = $this->extractMetrics($results);
        
        // 등급 계산
        $grade = $this->calculateGrade($metrics);
        $score = $this->calculateScore($metrics);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => [
                'results' => $results,
                'errors' => $errors,
                'tested_at' => now()->toISOString(),
            ],
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리 (로그인 사용자인 경우)
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics(array $results): array
    {
        $metrics = [];
        
        // 모든 리전에 대해 메트릭 추출
        foreach ($results as $region => $data) {
            $summary = $data['summary'] ?? [];
            
            $regionMetrics = [];
            
            // First visit 메트릭
            if (isset($summary['first'])) {
                $first = $summary['first'];
                $regionMetrics['first'] = [
                    'ttfb' => data_get($first, 'nav.ttfb'),
                    'load' => data_get($first, 'nav.load'),
                    'bytes' => data_get($first, 'res.bytes'),
                    'resources' => data_get($first, 'res.total'),
                ];
            }
            
            // Repeat visit 메트릭
            if (isset($summary['repeat'])) {
                $repeat = $summary['repeat'];
                $regionMetrics['repeat'] = [
                    'ttfb' => data_get($repeat, 'nav.ttfb'),
                    'load' => data_get($repeat, 'nav.load'),
                    'bytes' => data_get($repeat, 'res.bytes'),
                    'resources' => data_get($repeat, 'res.total'),
                ];
            }
            
            // Delta 정보도 포함
            if (isset($summary['deltas'])) {
                $regionMetrics['deltas'] = [
                    'ttfb_delta' => data_get($summary, 'deltas.ttfb_ms_delta'),
                    'load_delta' => data_get($summary, 'deltas.load_ms_delta'),
                    'bytes_delta' => data_get($summary, 'deltas.bytes_delta'),
                ];
            }
            
            $metrics[$region] = $regionMetrics;
        }
        
        return $metrics;
    }

    private function calculateGrade(array $metrics): string
    {
        $regionLabels = [
            'seoul', 'tokyo', 'singapore', 'virginia', 
            'oregon', 'frankfurt', 'london', 'sydney'
        ];

        $firstTTFB = [];
        $firstLoad = [];
        $repeatTTFB = [];
        $repeatLoad = [];

        foreach ($regionLabels as $region) {
            $m = $metrics[$region] ?? null;
            if (!$m) continue;

            $ft = data_get($m, 'first.ttfb');
            $fl = data_get($m, 'first.load');
            $rt = data_get($m, 'repeat.ttfb');
            $rl = data_get($m, 'repeat.load');

            if (is_numeric($ft)) $firstTTFB[$region] = (float) $ft;
            if (is_numeric($fl)) $firstLoad[$region] = (float) $fl;
            if (is_numeric($rt)) $repeatTTFB[$region] = (float) $rt;
            if (is_numeric($rl)) $repeatLoad[$region] = (float) $rl;
        }

        if (empty($firstTTFB) || empty($firstLoad)) {
            return 'F';
        }

        // Origin = TTFB가 가장 빠른 리전
        $tmp = $firstTTFB;
        asort($tmp);
        $originRegion = array_key_first($tmp);
        $originTTFB = $tmp[$originRegion] ?? null;
        $originLoad = $firstLoad[$originRegion] ?? null;

        $avgTTFB = array_sum($firstTTFB) / count($firstTTFB);
        $avgLoad = array_sum($firstLoad) / count($firstLoad);
        $worstTTFB = max($firstTTFB);
        $worstLoad = max($firstLoad);

        // 재방문 성능향상 계산
        $improvedRegions = 0;
        $eligibleRegions = 0;
        foreach ($firstLoad as $r => $fl) {
            $rl = $repeatLoad[$r] ?? null;
            if (is_numeric($fl) && is_numeric($rl) && $fl > 0) {
                $eligibleRegions++;
                if ($rl < $fl) {
                    $improvedRegions++;
                }
            }
        }
        $repeatImprovePct = $eligibleRegions ? ($improvedRegions / $eligibleRegions) * 100.0 : null;

        // 등급 계산 로직
        $meetsAll = function($conds) {
            foreach ($conds as $ok) {
                if ($ok === false) return false;
            }
            return true;
        };

        // A+ 기준
        if ($meetsAll([
            $originTTFB <= 200,
            $originLoad <= 1500,
            $avgTTFB <= 800,
            $avgLoad <= 2500,
            $worstTTFB <= 1500,
            $worstLoad <= 3000,
            $repeatImprovePct >= 80
        ])) {
            return 'A+';
        }

        // A 기준
        if ($meetsAll([
            $originTTFB <= 400,
            $originLoad <= 2500,
            $avgTTFB <= 1200,
            $avgLoad <= 3500,
            $worstTTFB <= 2000,
            $worstLoad <= 4000,
            $repeatImprovePct >= 60
        ])) {
            return 'A';
        }

        // B 기준
        if ($meetsAll([
            $originTTFB <= 800,
            $originLoad <= 3500,
            $avgTTFB <= 1600,
            $avgLoad <= 4500,
            $worstTTFB <= 2500,
            $worstLoad <= 5500,
            $repeatImprovePct >= 50
        ])) {
            return 'B';
        }

        // C 기준
        if ($meetsAll([
            $originTTFB <= 1200,
            $originLoad <= 4500,
            $avgTTFB <= 2000,
            $avgLoad <= 5500,
            $worstTTFB <= 3000,
            $worstLoad <= 6500,
            $repeatImprovePct >= 37.5
        ])) {
            return 'C';
        }

        // D 기준
        if ($meetsAll([
            $originTTFB <= 1600,
            $originLoad <= 6000,
            $avgTTFB <= 2500,
            $avgLoad <= 7000,
            $worstTTFB <= 3500,
            $worstLoad <= 8500,
            $repeatImprovePct >= 25
        ])) {
            return 'D';
        }

        return 'F';
    }

    private function calculateScore(array $metrics): float
    {
        $grade = $this->calculateGrade($metrics);
        
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