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
    /**
     * nuclei 실행 환경 보장:
     * - HOME 폴더
     * - templates 디렉토리
     * - cache 디렉토리
     * - ignore 파일
     */
    private function ensureNucleiEnv(): array
    {
        // 설치/운영 표준 경로 (없으면 기본값)
        $nucleiHome = env('NUCLEI_HOME', '/var/www/.nuclei'); // 또는 storage_path('.nuclei')
        // nuclei -update-templates 가 기본으로 설치하는 위치: <HOME>/nuclei-templates
        $templates  = env('NUCLEI_TEMPLATES', $nucleiHome . '/nuclei-templates');
        $configDir  = $nucleiHome;            // 보통 $HOME/.nuclei를 쓰지만, HOME만 고정해도 충분
        $cacheDir   = $nucleiHome . '/cache'; // 임의 캐시 경로

        foreach ([$nucleiHome, $templates, $cacheDir] as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
        }

        // ignore 파일이 없으면 빈 파일 생성
        $ignoreFile = $nucleiHome . '/.nuclei-ignore';
        if (!file_exists($ignoreFile)) {
            @touch($ignoreFile);
        }

        return [$nucleiHome, $templates, $configDir, $cacheDir, $ignoreFile];
    }

    private function testConnection(string $url): bool
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'HEAD',
                    'timeout' => 10,
                    'ignore_errors' => true,
                ],
            ]);
            $headers = @get_headers($url, 1, $context);
            return $headers !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function runTest($url, $testId)
    {
        // 1) 보안 검증
        $securityErrors = UrlSecurityValidator::validateWithDnsCheck($url);
        if (!empty($securityErrors)) {
            throw new \Exception('보안 검증 실패: ' . implode(', ', $securityErrors));
        }

        // 2) 연결 테스트
        if (!$this->testConnection($url)) {
            throw new \Exception('대상 URL에 연결할 수 없습니다.');
        }

        $test = null;

        try {
            $test = WebTest::find($testId);
            if (!$test) {
                throw new \Exception('Test not found with ID: ' . $testId);
            }

            // 3) 도메인 소유권 확인 (로그인 사용자만)
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

            // 4) Nuclei 실행
            $results = $this->performNucleiTest($url, $test);

            // 5) 결과 저장
            $this->parseAndSaveResults($test, $results);
        } catch (\Exception $e) {
            if ($test) {
                $test->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'error_message' => $e->getMessage(),
                ]);
            }
            throw $e;
        }
    }

    private function performNucleiTest($url, $test): array
    {
        $startTime = microtime(true);

        // nuclei 바이너리 탐색
        $possiblePaths = ['/usr/local/bin/nuclei', '/usr/bin/nuclei', '/home/ubuntu/go/bin/nuclei', 'nuclei'];
        $nucleiPath = null;
        foreach ($possiblePaths as $p) {
            if ($p === 'nuclei' || (file_exists($p) && is_executable($p))) {
                $nucleiPath = $p;
                break;
            }
        }
        if (!$nucleiPath) {
            throw new \RuntimeException("Nuclei 실행 파일을 찾을 수 없습니다.");
        }

        // 실행 환경 보장
        [$nucleiHome, $templatesDir, $nucleiConfigDir, $nucleiCacheDir, $ignoreFile] = $this->ensureNucleiEnv();

        // (선택) 템플릿 리스트 파일: 있으면 목록 기반(-t), 없으면 디렉터리 전체(-templates)
        $templateListFile = env('NUCLEI_TEMPLATE_LIST', $nucleiHome . '/nuclei-templates-2024-2025.txt');

        // 커맨드 조립
        $cmd = [
            $nucleiPath,
            '-u', $url, // 단일 URL 검사
            '-severity', 'critical,high,medium,low',
            '-jsonl',
            '-silent',
            '-duc',         // 원래 사용 옵션 유지
            '-ni',          // interactsh 비활성
            '-no-color',
            '-timeout', '10',
            '-retries', '0',
            '-rate-limit', '20',
            '-c', '10',
        ];

        // 템플릿 지정 로직
        $templateArgs = [];
        if (file_exists($templateListFile)) {
            foreach (file($templateListFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $tpl) {
                $tpl = trim($tpl);
                if ($tpl !== '') {
                    $templateArgs[] = '-t';
                    $templateArgs[] = $tpl;
                }
            }
        }
        if (empty($templateArgs)) {
            // 리스트 없거나 비면 디렉터리 전체 사용
            $templateArgs = ['-templates', $templatesDir];
        }
        $cmd = array_merge($cmd, $templateArgs);

        // 환경 변수: HOME 고정이 핵심
        $env = [
            'HOME' => $nucleiHome,
            // 아래 두 개는 지정하지 않아도 되지만, 지정한다면 HOME 하위로 통일하는 게 안전
            'NUCLEI_CONFIG_DIR' => $nucleiConfigDir,
            'NUCLEI_CACHE_DIR'  => $nucleiCacheDir,
            'PATH' => getenv('PATH'),
        ];

        // (디버깅이 필요할 때 주석 해제)
        // Log::info('Nuclei CMD', ['cmd' => $cmd, 'env' => $env]);

        // 실행 (작업 디렉터리는 앱 루트)
        $process = new Process($cmd, base_path(), $env, null, 180);
        $process->run();

        $duration = (int) round((microtime(true) - $startTime), 0);
        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();

        if (!$process->isSuccessful()) {
            // 흔한 원인 힌트
            $hint = [];
            if (str_contains($stderr, 'no templates provided')) {
                $hint[] = '템플릿이 비었습니다. `nuclei -update-templates` 후 템플릿 경로를 확인하세요.';
            }
            if (str_contains($stderr, '.nuclei-ignore') || str_contains($stderr, 'permission')) {
                $hint[] = "HOME({$nucleiHome}) 권한/경로를 확인하세요. {$ignoreFile} 존재해야 합니다.";
            }
            $hintMsg = $hint ? ' | HINT: ' . implode(' / ', $hint) : '';
            throw new \RuntimeException("Nuclei 실행 실패: {$stderr}{$hintMsg}");
        }

        // JSONL 파싱
        $buckets = ['critical' => [], 'high' => [], 'medium' => [], 'low' => [], 'info' => []];
        $templateDetails = [];

        foreach (explode("\n", trim($stdout)) as $line) {
            if ($line === '') {
                continue;
            }
            $data = json_decode($line, true);
            if (!$data || !isset($data['info'])) {
                continue;
            }

            $severity   = strtolower($data['info']['severity'] ?? 'info');
            $templateId = $data['template-id'] ?? '';
            $name       = $data['info']['name'] ?? '';
            $desc       = $data['info']['description'] ?? '';
            $matched    = $data['matched-at'] ?? $url;
            $reference  = (array)($data['info']['reference'] ?? []);

            $v = [
                'template_id' => $templateId,
                'name'        => $name,
                'description' => $desc,
                'matched_at'  => $matched,
                'severity'    => $severity,
                'reference'   => $reference,
            ];

            if (isset($buckets[$severity])) {
                $buckets[$severity][] = $v;
            }

            if ($templateId && !isset($templateDetails[$templateId])) {
                $templateDetails[$templateId] = [
                    'id'          => $templateId,
                    'name'        => $name,
                    'severity'    => $severity,
                    'description' => $desc,
                ];
            }
        }

        return [
            'vulnerabilities' => $buckets,
            'template_details' => $templateDetails,
            'duration' => $duration,
            'raw_output' => $stdout,
        ];
    }

    private function parseAndSaveResults($test, $data)
    {
        $vulnerabilities = $data['vulnerabilities'] ?? [];

        // 메트릭 저장
        $metrics = [
            'vulnerability_counts' => [
                'critical' => count($vulnerabilities['critical'] ?? []),
                'high'     => count($vulnerabilities['high'] ?? []),
                'medium'   => count($vulnerabilities['medium'] ?? []),
                'low'      => count($vulnerabilities['low'] ?? []),
                'info'     => count($vulnerabilities['info'] ?? []),
            ],
            'total_vulnerabilities' => array_sum([
                count($vulnerabilities['critical'] ?? []),
                count($vulnerabilities['high'] ?? []),
                count($vulnerabilities['medium'] ?? []),
                count($vulnerabilities['low'] ?? []),
            ]),
            'scan_duration'    => $data['duration'] ?? 0,
            'templates_matched'=> count($data['template_details'] ?? []),
        ];

        // 등급/점수
        $grade = $this->calculateGrade($vulnerabilities);
        $score = $this->calculateScore($vulnerabilities);

        $test->update([
            'status'        => 'completed',
            'finished_at'   => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results'       => [
                'vulnerabilities'  => $vulnerabilities,
                'template_details' => $data['template_details'] ?? [],
                'raw_output'       => $data['raw_output'] ?? '',
                'tested_at'        => now()->toISOString(),
            ],
            'metrics'       => $metrics,
        ]);

        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function calculateGrade(array $vulnerabilities): string
    {
        $critical = count($vulnerabilities['critical'] ?? []);
        $high     = count($vulnerabilities['high'] ?? []);
        $medium   = count($vulnerabilities['medium'] ?? []);
        $low      = count($vulnerabilities['low'] ?? []);

        if ($critical == 0 && $high == 0 && $medium == 0) {
            return 'A+';
        } elseif ($high <= 1 && $medium <= 1) {
            return 'A';
        } elseif ($high <= 2 || $medium <= 3) {
            return 'B';
        } elseif ($high >= 3 || $medium > 3) {
            return 'C';
        } elseif ($critical >= 1 || ($high > 3 && $medium > 5)) {
            return 'D';
        } else {
            return 'F';
        }
    }

    private function calculateScore(array $vulnerabilities): float
    {
        $critical = count($vulnerabilities['critical'] ?? []);
        $high     = count($vulnerabilities['high'] ?? []);
        $medium   = count($vulnerabilities['medium'] ?? []);
        $low      = count($vulnerabilities['low'] ?? []);

        $baseScore = 100;
        $deduction = ($critical * 30) + ($high * 20) + ($medium * 10) + ($low * 3);
        $score     = max(0, $baseScore - $deduction);

        if ($score > 0) {
            $grade = $this->calculateGrade($vulnerabilities);
            switch ($grade) {
                case 'A+': $score = min(100, max(90, $score)); break;
                case 'A':  $score = min(89,  max(80, $score)); break;
                case 'B':  $score = min(79,  max(70, $score)); break;
                case 'C':  $score = min(69,  max(60, $score)); break;
                case 'D':  $score = min(59,  max(50, $score)); break;
                case 'F':  $score = min(49,  $score);          break;
            }
        }
        return round($score, 1);
        }

    /**
     * 템플릿 정보를 캐시에서 가져오기
     * - 리스트 파일이 있으면 해당 목록만
     * - 없으면 설치 디렉터리 전체 스캔
     */
    public function getTemplateInfo(): array
    {
        return Cache::remember('nuclei_templates_2024_2025', 86400, function () {
            $nucleiHome       = env('NUCLEI_HOME', '/var/www/.nuclei');
            $templateListFile = env('NUCLEI_TEMPLATE_LIST', $nucleiHome . '/nuclei-templates-2024-2025.txt');
            // 설치 경로는 nuclei-templates (중요!)
            $templatesDir     = env('NUCLEI_TEMPLATES', $nucleiHome . '/nuclei-templates');

            $paths = [];

            if (file_exists($templateListFile)) {
                foreach (file($templateListFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $p) {
                    $p = trim($p);
                    if ($p) $paths[] = $p;
                }
            }

            // 리스트가 없으면 디렉터리 전체 스캔
            if (empty($paths) && is_dir($templatesDir)) {
                $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($templatesDir));
                foreach ($it as $f) {
                    if ($f->isFile() && str_ends_with($f->getFilename(), '.yaml')) {
                        $paths[] = $f->getPathname();
                    }
                }
            }

            $result = [];
            foreach ($paths as $path) {
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

            return $result;
        });
    }
}