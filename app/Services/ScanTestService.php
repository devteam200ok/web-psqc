<?php

namespace App\Services;

use App\Models\WebTest;
use App\Models\Domain;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class ScanTestService
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
            $test = WebTest::find($testId);
            
            if (!$test) {
                throw new \Exception('Test not found with ID: ' . $testId);
            }
            
            // 도메인 소유권 확인 (user_id가 있는 경우에만)
            if ($test->user_id) {
                $domain = parse_url($url, PHP_URL_HOST);
                if (!$domain) {
                    throw new \Exception('올바른 URL 형식이 아닙니다.');
                }

                $verifiedDomain = Domain::where('user_id', $test->user_id)
                    ->where('is_verified', true)
                    ->whereRaw('? LIKE CONCAT("%", SUBSTRING_INDEX(SUBSTRING_INDEX(url, "://", -1), "/", 1), "%")', [$domain])
                    ->first();

                if (!$verifiedDomain) {
                    throw new \Exception('도메인 소유권 인증이 필요합니다.');
                }
            }
            
            $test->update(['status' => 'running']);

            $results = $this->performScanTest($url);
            
            if (empty($results['vulnerabilities']) && empty($results['technologies'])) {
                throw new \Exception('스캔 결과를 파싱할 수 없습니다.');
            }
            
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

    private function performScanTest($url): array
    {
        $disk = Storage::disk('local');
        $dir = 'scan';
        
        if (!$disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        $scanId = 'scan_' . Str::uuid();
        $planRel = "{$dir}/plan-{$scanId}.yaml";
        $reportRel = "{$dir}/report-{$scanId}.json";
        $planPath = $disk->path($planRel);
        $reportPath = $disk->path($reportRel);

        // YAML 플랜 생성
        $yaml = $this->buildScanPlan($url, $reportPath);
        $disk->put($planRel, $yaml);

        // ZAP 실행
        $cmd = [
            '/opt/zap2/zap.sh', '-cmd', '-silent',
            '-autorun', $planPath,
            '-config', 'network.connectionTimeout=4000',
            '-config', 'network.readTimeout=6000',
            '-nostdout',
        ];
        
        $env = [
            'HOME' => '/var/www',
            'JAVA_OPTS' => '-Xmx512m -Xms256m -XX:+UseG1GC',
        ];

        $process = new Process($cmd, base_path(), $env, null, 60);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("ZAP 실행 실패: " . $process->getErrorOutput());
        }

        if (!$disk->exists($reportRel)) {
            throw new \RuntimeException("보고서 파일 생성 실패");
        }

        // JSON 결과 파싱
        $jsonContent = $disk->get($reportRel);
        $data = json_decode($jsonContent, true);
        
        if (!$data || !isset($data['site'])) {
            throw new \Exception('Invalid JSON format');
        }

        $vulnerabilities = $this->extractVulnerabilities($data);
        $technologies = $this->extractTechnologies($data);
        
        // 파일 정리
        $disk->delete($planRel);
        $disk->delete($reportRel);

        return [
            'vulnerabilities' => $vulnerabilities,
            'technologies' => $technologies,
            'raw_data' => $data
        ];
    }

    private function buildScanPlan(string $targetUrl, string $reportAbsPath): string
    {
        $reportDir = dirname($reportAbsPath);
        $reportFile = basename($reportAbsPath);

        return <<<YAML
env:
  contexts:
    - name: ctx
      urls:
        - {$targetUrl}
      includePaths:
        - {$targetUrl}/
      excludePaths:
        - {$targetUrl}/.*
  parameters:
    progressToStdout: true
    failOnError: false
    failOnWarning: false

jobs:
  - type: spider
    parameters:
      context: ctx
      maxDuration: 1
      maxChildren: 1
      maxDepth: 0

  - type: passiveScan-wait
    parameters:
      maxDuration: 2

  - type: report
    parameters:
      template: "traditional-json"
      reportDir: "{$reportDir}"
      reportFile: "{$reportFile}"
      reportTitle: "Security Scan Report"
      displayReport: false
YAML;
    }

    private function extractVulnerabilities(array $data): array
    {
        $vulnerabilities = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
            'informational' => 0,
            'details' => []
        ];

        if (!isset($data['site']) || !is_array($data['site'])) {
            return $vulnerabilities;
        }

        foreach ($data['site'] as $site) {
            if (isset($site['alerts']) && is_array($site['alerts'])) {
                foreach ($site['alerts'] as $alert) {
                    $vulnName = $alert['name'] ?? 'Unknown';
                    
                    // Tech Detected는 취약점에서 제외
                    if (stripos($vulnName, 'tech detected') !== false) {
                        continue;
                    }
                    
                    // CSP 관련 취약점 제외
                    if (stripos($vulnName, 'content security policy') !== false || 
                        stripos($vulnName, 'csp') !== false) {
                        continue;
                    }

                    $riskDesc = $alert['riskdesc'] ?? '';
                    $riskLevel = $this->parseRiskLevel($riskDesc, $vulnName);

                    switch ($riskLevel) {
                        case 'critical':
                            $vulnerabilities['critical']++;
                            break;
                        case 'high':
                            $vulnerabilities['high']++;
                            break;
                        case 'medium':
                            $vulnerabilities['medium']++;
                            break;
                        case 'low':
                            $vulnerabilities['low']++;
                            break;
                        default:
                            $vulnerabilities['informational']++;
                    }

                    $vulnerabilities['details'][] = [
                        'name' => $vulnName,
                        'risk' => $riskLevel,
                        'confidence' => $alert['confidence'] ?? '',
                        'description' => $alert['desc'] ?? '',
                        'solution' => $alert['solution'] ?? '',
                        'instances' => count($alert['instances'] ?? [])
                    ];
                }
            }
        }

        return $vulnerabilities;
    }

    private function extractTechnologies(array $data): array
    {
        $technologies = [];

        if (!isset($data['site']) || !is_array($data['site'])) {
            return $technologies;
        }

        foreach ($data['site'] as $site) {
            if (isset($site['alerts']) && is_array($site['alerts'])) {
                foreach ($site['alerts'] as $alert) {
                    $alertName = $alert['name'] ?? '';
                    
                    // Tech Detected 항목만 추출
                    if (stripos($alertName, 'tech detected') !== false) {
                        // "Tech Detected - " 부분 제거
                        $techName = trim(str_ireplace('tech detected', '', $alertName));
                        $techName = ltrim($techName, ' -');
                        
                        $description = $alert['desc'] ?? '';
                        
                        // 카테고리 추출 (description에서)
                        $category = 'Miscellaneous';
                        if (preg_match('/The following "([^"]+)" technology/i', $description, $matches)) {
                            $category = $matches[1];
                        }
                        
                        // 설명 추출
                        $techDescription = '';
                        if (preg_match('/Described as:<\/p><p>(.+?)<\/p>/s', $description, $matches)) {
                            $techDescription = strip_tags($matches[1]);
                        }
                        
                        $technologies[] = [
                            'name' => $techName,
                            'category' => $category,
                            'description' => $techDescription,
                            'instances' => count($alert['instances'] ?? [])
                        ];
                    }
                }
            }
        }

        return $technologies;
    }

    private function parseRiskLevel(string $riskDesc, string $vulnName): string
    {
        $risk = strtolower(trim($riskDesc));

        // ▼ 특정 항목은 등급을 무조건 Low로 강등
        $forceLowPatterns = [
            'cross-domain javascript source file inclusion',
            'session management response identified',
        ];
        foreach ($forceLowPatterns as $pattern) {
            if (stripos($vulnName, $pattern) !== false) {
                return 'low';
            }
        }

        // 일부 High를 Medium으로 조정
        $mediumPatterns = [
            'strict-transport-security',
            'x-content-type-options',
            'x-frame-options',
            'anti-clickjacking'
        ];
        foreach ($mediumPatterns as $pattern) {
            if (stripos($vulnName, $pattern) !== false && strpos($risk, 'high') !== false) {
                return 'medium';
            }
        }

        if (strpos($risk, 'critical') !== false) {
            return 'critical';
        } elseif (strpos($risk, 'high') !== false) {
            return 'high';
        } elseif (strpos($risk, 'medium') !== false) {
            return 'medium';
        } elseif (strpos($risk, 'low') !== false) {
            return 'low';
        }

        return 'informational';
    }

    private function parseAndSaveResults($test, $data)
    {
        $vulnerabilities = $data['vulnerabilities'];
        $technologies = $data['technologies'];
        
        // 메트릭을 JSON 형태로 저장
        $metrics = [
            'vulnerability_counts' => [
                'critical' => $vulnerabilities['critical'],
                'high' => $vulnerabilities['high'],
                'medium' => $vulnerabilities['medium'],
                'low' => $vulnerabilities['low'],
                'informational' => $vulnerabilities['informational']
            ],
            'total_vulnerabilities' => array_sum([
                $vulnerabilities['critical'],
                $vulnerabilities['high'],
                $vulnerabilities['medium'],
                $vulnerabilities['low']
            ]),
            'technologies_detected' => count($technologies)
        ];
        
        // 등급 계산 (Tech Detected는 제외되므로 영향 없음)
        $grade = $this->calculateGrade($vulnerabilities);
        $score = $this->calculateScore($vulnerabilities);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => [
                'vulnerabilities' => $vulnerabilities,
                'technologies' => $technologies,
                'scan_data' => $data['raw_data'] ?? [],
                'tested_at' => now()->toISOString(),
            ],
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function calculateGrade(array $vulnerabilities): string
    {
        $critical = $vulnerabilities['critical'];
        $high = $vulnerabilities['high'];
        $medium = $vulnerabilities['medium'];

        if ($critical == 0 && $high == 0 && $medium == 0) {
            return 'A+';
        } elseif ($critical == 0 && $high == 0 && $medium <= 1) {
            return 'A';
        } elseif ($critical == 0 && $high <= 1 && $medium <= 2) {
            return 'B';
        } elseif ($critical == 0 && ($high >= 2 || $medium >= 3)) {
            return 'C';
        } elseif ($critical >= 1 || $high >= 3) {
            return 'D';
        } else {
            return 'F';
        }
    }

    private function calculateScore(array $vulnerabilities): float
    {
        $critical = $vulnerabilities['critical'];
        $high = $vulnerabilities['high'];
        $medium = $vulnerabilities['medium'];
        $low = $vulnerabilities['low'];

        // 100점 만점 기준
        $baseScore = 100;
        
        // 취약점별 감점
        $deduction = ($critical * 25) + ($high * 15) + ($medium * 8) + ($low * 2);
        
        $score = max(0, $baseScore - $deduction);

        // 등급별 점수 범위 조정
        if ($score > 0) {
            $grade = $this->calculateGrade($vulnerabilities);
            
            switch($grade) {
                case 'A+':
                    $score = min(100, max(90, $score));
                    break;
                case 'A':
                    $score = min(89, max(80, $score));
                    break;
                case 'B':
                    $score = min(79, max(70, $score));
                    break;
                case 'C':
                    $score = min(69, max(60, $score));
                    break;
                case 'D':
                    $score = min(59, max(50, $score));
                    break;
                case 'F':
                    $score = min(49, $score);
                    break;
            }
        }

        return round($score, 1);
    }
}