<?php

namespace App\Services;

use App\Models\WebTest;
use App\Models\Domain;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Cache;

class NucleiTestService
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

            $results = $this->performNucleiTest($url, $test);
            
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

    private function performNucleiTest($url, $test): array
    {
        $startTime = time();
        
        // Nuclei 실행 파일 경로 확인
        $possiblePaths = [
            '/usr/local/bin/nuclei',
            '/home/ubuntu/go/bin/nuclei',
            '/usr/bin/nuclei',
            'nuclei'
        ];
        
        $nucleiPath = null;
        foreach ($possiblePaths as $path) {
            if ($path === 'nuclei' || (file_exists($path) && is_executable($path))) {
                $nucleiPath = $path;
                break;
            }
        }
        
        if (!$nucleiPath) {
            throw new \RuntimeException("Nuclei 실행 파일을 찾을 수 없습니다.");
        }

        // Nuclei 설정 디렉토리 생성 (존재하지 않으면)
        $nucleiConfigDir = '/tmp/.nuclei';
        $nucleiCacheDir = '/tmp/.nuclei-cache';
        
        if (!is_dir($nucleiConfigDir)) {
            mkdir($nucleiConfigDir, 0777, true);
        }
        if (!is_dir($nucleiCacheDir)) {
            mkdir($nucleiCacheDir, 0777, true);
        }

        // 최신 템플릿 리스트 파일 (사전에 준비된 파일)
        $templateList = '/tmp/nuclei-templates-2024-2025.txt';
        if (!file_exists($templateList)) {
            // 템플릿 리스트가 없으면 기본 템플릿 사용
            $templates = [];
        } else {
            $templates = [];
            foreach (file($templateList, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $tpl) {
                $templates[] = "-t";
                $templates[] = $tpl;
            }
        }

        // Nuclei 커맨드 구성
        $cmd = array_merge([
            $nucleiPath,
            '-target', $url,
            '-severity', 'critical,high,medium,low',
            '-jsonl',
            '-silent',
            '-duc',
            '-ni',
            '-no-color',
            '-timeout', '10',
            '-retries', '0',
            '-rate-limit', '20',
            '-c', '10',
        ], $templates);

        $env = [
            'HOME' => '/tmp',
            'NUCLEI_CONFIG_DIR' => $nucleiConfigDir,
            'NUCLEI_CACHE_DIR'  => $nucleiCacheDir,
            'PATH' => getenv('PATH'),
        ];

        // 프로세스 실행
        $process = new Process($cmd, null, $env, null, 150);
        $process->run();

        $duration = time() - $startTime;
        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Nuclei 실행 실패: " . $stderr);
        }

        // JSONL 파싱
        $vulnerabilities = [
            'critical' => [],
            'high' => [],
            'medium' => [],
            'low' => [],
            'info' => []
        ];
        
        $templateDetails = [];

        foreach (explode("\n", trim($stdout)) as $line) {
            if (!$line) continue;
            
            $data = json_decode($line, true);
            if (!$data || !isset($data['info'])) continue;

            $severity = strtolower($data['info']['severity'] ?? 'info');
            $templateId = $data['template-id'] ?? '';
            $name = $data['info']['name'] ?? '';
            $desc = $data['info']['description'] ?? '';
            $matched = $data['matched-at'] ?? $url;
            $reference = $data['info']['reference'] ?? [];

            $vuln = [
                'template_id' => $templateId,
                'name' => $name,
                'description' => $desc,
                'matched_at' => $matched,
                'severity' => $severity,
                'reference' => $reference
            ];
            
            if (isset($vulnerabilities[$severity])) {
                $vulnerabilities[$severity][] = $vuln;
            }
            
            // 템플릿 정보 수집
            if (!isset($templateDetails[$templateId])) {
                $templateDetails[$templateId] = [
                    'id' => $templateId,
                    'name' => $name,
                    'severity' => $severity,
                    'description' => $desc
                ];
            }
        }

        return [
            'vulnerabilities' => $vulnerabilities,
            'template_details' => $templateDetails,
            'duration' => $duration,
            'raw_output' => $stdout
        ];
    }

    private function parseAndSaveResults($test, $data)
    {
        $vulnerabilities = $data['vulnerabilities'];
        
        // 메트릭을 JSON 형태로 저장
        $metrics = [
            'vulnerability_counts' => [
                'critical' => count($vulnerabilities['critical'] ?? []),
                'high' => count($vulnerabilities['high'] ?? []),
                'medium' => count($vulnerabilities['medium'] ?? []),
                'low' => count($vulnerabilities['low'] ?? []),
                'info' => count($vulnerabilities['info'] ?? [])
            ],
            'total_vulnerabilities' => array_sum([
                count($vulnerabilities['critical'] ?? []),
                count($vulnerabilities['high'] ?? []),
                count($vulnerabilities['medium'] ?? []),
                count($vulnerabilities['low'] ?? [])
            ]),
            'scan_duration' => $data['duration'] ?? 0,
            'templates_matched' => count($data['template_details'] ?? [])
        ];
        
        // 등급 계산
        $grade = $this->calculateGrade($vulnerabilities);
        $score = $this->calculateScore($vulnerabilities);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => [
                'vulnerabilities' => $vulnerabilities,
                'template_details' => $data['template_details'] ?? [],
                'raw_output' => $data['raw_output'] ?? '',
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
        $critical = count($vulnerabilities['critical'] ?? []);
        $high = count($vulnerabilities['high'] ?? []);
        $medium = count($vulnerabilities['medium'] ?? []);
        $low = count($vulnerabilities['low'] ?? []);

        // 첨부된 등급 기준표에 따른 판정
        if ($critical == 0 && $high == 0 && $medium == 0) {
            // 2024-2025 CVE 미검출, 보안 헤더 양호
            return 'A+';
        } elseif ($high <= 1 && $medium <= 1) {
            // 최근 CVE 직접 노출 없음, 경미한 설정 경고
            return 'A';
        } elseif ($high <= 2 || $medium <= 3) {
            // 일부 구성 노출/배너 노출, 패치 지연 경향
            return 'B';
        } elseif ($high >= 3 || $medium > 3) {
            // 민감 파일/백업 노출, 구버전 컴포넌트
            return 'C';
        } elseif ($critical >= 1 || ($high > 3 && $medium > 5)) {
            // Critical 존재 또는 High 다수, 최근 CVE 직접 영향
            return 'D';
        } else {
            // 다수의 Critical/High, 광범위 노출
            return 'F';
        }
    }

    private function calculateScore(array $vulnerabilities): float
    {
        $critical = count($vulnerabilities['critical'] ?? []);
        $high = count($vulnerabilities['high'] ?? []);
        $medium = count($vulnerabilities['medium'] ?? []);
        $low = count($vulnerabilities['low'] ?? []);

        // 100점 만점 기준
        $baseScore = 100;
        
        // 취약점별 감점
        $deduction = ($critical * 30) + ($high * 20) + ($medium * 10) + ($low * 3);
        
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

    /**
     * 템플릿 정보를 캐시에서 가져오기
     */
    public function getTemplateInfo(): array
    {
        return Cache::remember('nuclei_templates_2024_2025', 86400, function () {
            $templateFile = "/tmp/nuclei-templates-2024-2025.txt";
            $result = [];

            if (file_exists($templateFile)) {
                foreach (file($templateFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $path) {
                    if (!file_exists($path)) continue;

                    try {
                        $yaml = Yaml::parseFile($path);

                        $result[] = [
                            'id'          => $yaml['id'] ?? basename($path, '.yaml'),
                            'name'        => $yaml['info']['name'] ?? '',
                            'severity'    => $yaml['info']['severity'] ?? 'info',
                            'description' => $yaml['info']['description'] ?? '',
                            'reference'   => (array)($yaml['info']['reference'] ?? []),
                            'author'      => $yaml['info']['author'] ?? '',
                            'path'        => $path,
                        ];
                    } catch (\Throwable $e) {
                        Log::warning("Failed to parse template: " . $path);
                    }
                }
            }

            return $result;
        });
    }
}