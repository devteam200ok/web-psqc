<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use App\Validators\UrlSecurityValidator;

class LoadTestService
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

            $results = $this->performK6Test($url, $test->test_config);
            
            if (empty($results)) {
                throw new \Exception('K6 test failed to produce results');
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
            
            Log::error('Load test failed: ' . $e->getMessage(), [
                'test_id' => $testId,
                'url' => $url,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    private function performK6Test($url, $config): array
    {
        $vus = $config['vus'] ?? 50;
        $duration = $config['duration_seconds'] ?? 45;
        $thinkMin = $config['think_time_min'] ?? 3;
        $thinkMax = $config['think_time_max'] ?? 10;

        // 임시 디렉토리 생성
        $tmpDir = storage_path('app/k6/' . Str::uuid());
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }
        
        $scriptPath = $tmpDir . '/script.js';
        $jsonPath = $tmpDir . '/results.json';

        // K6 스크립트 생성
        $script = $this->generateK6Script($url, $vus, $duration, $thinkMin, $thinkMax);
        file_put_contents($scriptPath, $script);

        try {
            // K6 실행
            $command = sprintf(
                'k6 run --summary-export %s %s 2>&1',
                escapeshellarg($jsonPath),
                escapeshellarg($scriptPath)
            );

            $result = Process::timeout(max(120, $duration + 60))->run($command);
            
            $exitCode = $result->exitCode();
            $output = $result->output();
            $errorOutput = $result->errorOutput();

            // 결과 파싱
            if (file_exists($jsonPath)) {
                $jsonData = json_decode(file_get_contents($jsonPath), true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON output from K6');
                }
                
                return $jsonData;
            } else {
                throw new \Exception('K6 output file not found. Exit code: ' . $exitCode . '. Output: ' . $output);
            }

        } finally {
            // 임시 파일 정리
            if (is_dir($tmpDir)) {
                exec('rm -rf ' . escapeshellarg($tmpDir));
            }
        }
    }

    private function generateK6Script($url, $vus, $duration, $thinkMin, $thinkMax): string
    {
        return <<<JS
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: {$vus},
  duration: '{$duration}s',
};

export default function () {
  const res = http.get('{$url}', { 
    redirects: 5,
    timeout: '30s',
    headers: {
      'User-Agent': 'DevTeam-K6-LoadTest/1.0'
    }
  });
  
  check(res, { 
    'status is 2xx/3xx': (r) => r.status >= 200 && r.status < 400,
    'response time OK': (r) => r.timings.duration < 5000, // 5초 이내
    'content length > 0': (r) => r.body.length > 0,
  });
  
  // {$thinkMin}-{$thinkMax}초 랜덤 Think Time
  const thinkTime = Math.random() * ({$thinkMax} - {$thinkMin}) + {$thinkMin};
  sleep(thinkTime);
}
JS;
    }

    private function parseAndSaveResults($test, $jsonData)
    {
        // 메트릭 추출
        $metrics = $this->extractMetrics($jsonData);
        
        // 등급 계산
        $grade = $this->calculateGrade($metrics, $test->test_config);
        $score = $this->calculateScore($grade, $test->test_config);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => $jsonData,
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리 (로그인 사용자인 경우)
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics($jsonData): array
    {
        $metrics = [];
        
        // HTTP 요청 수
        $metrics['http_reqs'] = data_get($jsonData, 'metrics.http_reqs.count')
            ?? data_get($jsonData, 'metrics.http_reqs.values.count') ?? 0;
        
        $metrics['http_reqs_rate'] = data_get($jsonData, 'metrics.http_reqs.rate')
            ?? data_get($jsonData, 'metrics.http_reqs.values.rate') ?? 0;
        
        // 실패율
        $metrics['http_req_failed'] = data_get($jsonData, 'metrics.http_req_failed.value')
            ?? data_get($jsonData, 'metrics.http_req_failed.values.rate') ?? 0;
        
        // 응답 시간
        $durationValues = data_get($jsonData, 'metrics.http_req_duration.values')
            ?? data_get($jsonData, 'metrics.http_req_duration');
        
        if (is_array($durationValues)) {
            $metrics['http_req_duration_avg'] = $durationValues['avg'] ?? 0;
            $metrics['http_req_duration_min'] = $durationValues['min'] ?? 0;
            $metrics['http_req_duration_max'] = $durationValues['max'] ?? 0;
            $metrics['http_req_duration_med'] = $durationValues['med'] ?? 0;
            $metrics['http_req_duration_p90'] = $durationValues['p(90)'] ?? 0;
            $metrics['http_req_duration_p95'] = $durationValues['p(95)'] ?? 0;
        }
        
        // 반복 수
        $metrics['iterations'] = data_get($jsonData, 'metrics.iterations.count')
            ?? data_get($jsonData, 'metrics.iterations.values.count') ?? 0;
        
        // VUs
        $metrics['vus_max'] = data_get($jsonData, 'metrics.vus_max.value')
            ?? data_get($jsonData, 'metrics.vus_max.values.value') ?? 0;
        
        // 데이터 전송량
        $metrics['data_received'] = data_get($jsonData, 'metrics.data_received.count')
            ?? data_get($jsonData, 'metrics.data_received.values.count') ?? 0;
        
        $metrics['data_sent'] = data_get($jsonData, 'metrics.data_sent.count')
            ?? data_get($jsonData, 'metrics.data_sent.values.count') ?? 0;
        
        // 체크 결과
        $metrics['checks_passes'] = data_get($jsonData, 'metrics.checks.passes')
            ?? data_get($jsonData, 'metrics.checks.values.passes') ?? 0;
        
        $metrics['checks_fails'] = data_get($jsonData, 'metrics.checks.fails')
            ?? data_get($jsonData, 'metrics.checks.values.fails') ?? 0;
        
        return $metrics;
    }

    private function calculateGrade(array $metrics, array $config): string
    {
        $vus = $config['vus'] ?? 50;
        $duration = $config['duration_seconds'] ?? 45;
        
        // VU 수와 Duration에 따른 최대 등급 제한
        $vuGrade = 'F';
        if ($vus >= 100) $vuGrade = 'A+';
        elseif ($vus >= 50) $vuGrade = 'A';
        elseif ($vus >= 30) $vuGrade = 'B';
        else $vuGrade = 'C';

        $durationGrade = 'F';
        if ($duration >= 60) $durationGrade = 'A+';
        elseif ($duration >= 45) $durationGrade = 'A';
        elseif ($duration >= 30) $durationGrade = 'B';
        else $durationGrade = 'C';

        // 더 낮은 등급으로 제한
        $gradeOrder = ['F' => 0, 'D' => 1, 'C' => 2, 'B' => 3, 'A' => 4, 'A+' => 5];
        $maxGradeValue = min($gradeOrder[$vuGrade], $gradeOrder[$durationGrade]);
        $maxGrade = array_search($maxGradeValue, $gradeOrder);
        
        // 기본 메트릭 추출
        $p95Response = $metrics['http_req_duration_p95'] ?? 0;
        $p90Response = $metrics['http_req_duration_p90'] ?? 0;
        $avgResponse = $metrics['http_req_duration_avg'] ?? 0; // 중간값 대신 평균값 사용
        $failureRate = ($metrics['http_req_failed'] ?? 0) * 100; // 백분율로 변환
        
        // 안정성 계산 (P90과 평균값의 비율) - 화면 기준에 맞춤
        $stabilityRatio = $avgResponse > 0 ? ($p90Response / $avgResponse) : 999;
        
        // 등급별 기준 체크
        $criteria = $this->getGradeCriteria();
        
        foreach (['A+', 'A', 'B', 'C', 'D'] as $grade) {
            // VU/Duration 제한 체크
            if ($gradeOrder[$grade] > $maxGradeValue) continue;
            
            $c = $criteria[$grade];
            
            if ($p95Response <= $c['max_p95_ms'] &&
                $failureRate <= $c['max_error_rate'] &&
                $stabilityRatio <= $c['max_stability_ratio']) {
                return $grade;
            }
        }
        
        return 'F';
    }
    
    private function getGradeCriteria(): array
    {
        return [
            'A+' => [
                'max_p95_ms' => 1000,
                'max_error_rate' => 0.1,
                'max_stability_ratio' => 2.0  // P90 ≤ 평균값의 200%
            ],
            'A' => [
                'max_p95_ms' => 1200,
                'max_error_rate' => 0.5,
                'max_stability_ratio' => 2.4  // P90 ≤ 평균값의 240%
            ],
            'B' => [
                'max_p95_ms' => 1500,
                'max_error_rate' => 1.0,
                'max_stability_ratio' => 2.8  // P90 ≤ 평균값의 280%
            ],
            'C' => [
                'max_p95_ms' => 2000,
                'max_error_rate' => 2.0,
                'max_stability_ratio' => 3.2  // P90 ≤ 평균값의 320%
            ],
            'D' => [
                'max_p95_ms' => 3000,
                'max_error_rate' => 5.0,
                'max_stability_ratio' => 4.0  // P90 ≤ 평균값의 400%
            ]
        ];
    }

    private function calculateScore(string $grade, array $config): float
    {
        $vus = $config['vus'] ?? 50;
        $duration = $config['duration_seconds'] ?? 45;
        
        // VU와 Duration에 따른 최대 점수 제한
        $maxScore = 50;
        
        if ($vus >= 100 && $duration >= 60) {
            $maxScore = 100;
        } elseif ($vus >= 50 && $duration >= 45) {
            $maxScore = 90;
        } elseif ($vus >= 30 && $duration >= 30) {
            $maxScore = 80;
        } elseif ($vus >= 20 && $duration >= 30) {
            $maxScore = 70;
        } elseif ($vus >= 10 && $duration >= 30) {
            $maxScore = 60;
        }
        
        $baseScore = match($grade) {
            'A+' => rand(90, 100),
            'A' => rand(80, 89),
            'B' => rand(70, 79),
            'C' => rand(60, 69),
            'D' => rand(50, 59),
            default => rand(0, 49)
        };
        
        // 제한 적용
        return min($baseScore, $maxScore);
    }
}