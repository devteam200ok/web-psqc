<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;
use Symfony\Component\Process\Process;

class AccessibilityTestService
{
    // 점수 가중치 (100점 만점 기준)
    private int $SCORE_MAX = 100;
    private int $W_CRIT = 20;  
    private int $W_SER  = 8;
    private int $W_MOD  = 4;
    private int $W_MIN  = 1;

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

            // axe-core 스크립트 실행
            $results = $this->runAxeCore($url);
            
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

    private function runAxeCore($url): array
    {
        $script = base_path('scripts/axe_accessibility.mjs');
        
        if (!file_exists($script) || !is_readable($script)) {
            throw new \Exception("스캔 스크립트를 찾을 수 없습니다: {$script}");
        }

        $cmd = ['node', $script, $url];
        $env = [
            'PATH' => getenv('PATH'),
            'HOME' => '/var/www',
            'NPM_CONFIG_CACHE' => '/var/www/.npm',
            'npm_config_cache' => '/var/www/.npm',
            'NPX_CACHE_DIR' => '/var/www/.npm/_npx',
            'PUPPETEER_CACHE_DIR' => base_path('storage/app/private/puppeteer'),
            'PUPPETEER_DOWNLOAD_PATH' => base_path('storage/app/private/puppeteer'),
        ];

        $process = new Process($cmd, base_path('scripts'), $env, null, 120);
        $process->run();

        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();

        if (!$process->isSuccessful()) {
            throw new \Exception("axe-core 실행 실패\n{$stderr}\n{$stdout}");
        }

        // 저장 경로 추출
        $savedPath = $this->extractSavedPath($stdout);
        if (!$savedPath || !file_exists($savedPath)) {
            throw new \Exception("결과 JSON 파일을 찾을 수 없습니다. stdout:\n{$stdout}");
        }

        $json = file_get_contents($savedPath);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \Exception("JSON 파싱 실패: {$savedPath}");
        }

        // violations 추출
        $violations = $this->extractViolations($data);

        // 카운트 집계
        $counts = $this->countViolations($violations);

        return [
            'violations' => $violations,
            'counts' => $counts,
            'raw_json' => $json,
            'saved_path' => $savedPath,
        ];
    }

    private function extractSavedPath(string $stdout): ?string
    {
        if (preg_match('~Saved:\s*(/[^\\s]+?\.json)~', $stdout, $m)) {
            return $m[1];
        }
        if (preg_match('~Saving JSON to:\s*(/[^\\s]+?\.json)~', $stdout, $m)) {
            return $m[1];
        }
        return null;
    }

    private function extractViolations(array $data): array
    {
        // 최상위가 배열인 경우
        if (array_is_list($data)) {
            $out = [];
            foreach ($data as $entry) {
                if (isset($entry['violations']) && is_array($entry['violations'])) {
                    $out = array_merge($out, $entry['violations']);
                    continue;
                }
                if (isset($entry['results'][0]['violations']) && is_array($entry['results'][0]['violations'])) {
                    $out = array_merge($out, $entry['results'][0]['violations']);
                    continue;
                }
                if (isset($entry['pages']) && is_array($entry['pages'])) {
                    foreach ($entry['pages'] as $p) {
                        if (isset($p['violations']) && is_array($p['violations'])) {
                            $out = array_merge($out, $p['violations']);
                        }
                    }
                }
            }
            return $out;
        }

        // 최상위에 violations
        if (isset($data['violations']) && is_array($data['violations'])) {
            return $data['violations'];
        }

        // results[0].violations
        if (isset($data['results'][0]['violations']) && is_array($data['results'][0]['violations'])) {
            return $data['results'][0]['violations'];
        }

        // pages[*].violations
        if (isset($data['pages']) && is_array($data['pages'])) {
            $out = [];
            foreach ($data['pages'] as $p) {
                if (isset($p['violations']) && is_array($p['violations'])) {
                    $out = array_merge($out, $p['violations']);
                }
            }
            return $out;
        }

        return [];
    }

    private function countViolations(array $violations): array
    {
        $counts = [
            'critical' => 0,
            'serious'  => 0,
            'moderate' => 0,
            'minor'    => 0,
            'total'    => 0,
        ];

        foreach ($violations as $v) {
            $impact = strtolower($v['impact'] ?? 'minor');
            if (!isset($counts[$impact])) {
                $impact = 'minor';
            }
            $counts[$impact]++;
        }

        $counts['total'] = array_sum([
            $counts['critical'], 
            $counts['serious'], 
            $counts['moderate'], 
            $counts['minor']
        ]);

        return $counts;
    }

    private function parseAndSaveResults($test, $data)
    {
        $violations = $data['violations'] ?? [];
        $counts = $data['counts'] ?? [];
        
        // 메트릭 구성
        $metrics = [
            'violations_count' => $counts,
            'violations_detail' => $this->normalizeViolations($violations),
            'saved_path' => $data['saved_path'] ?? null,
        ];
        
        // 등급 계산
        $grade = $this->calculateGrade($counts);
        $score = $this->calculateScore($counts);
        
        // 결과 저장
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => [
                // 'raw_json' => $data['raw_json'] ?? null,
                'saved_path' => $data['saved_path'] ?? null,
                'tested_at' => now()->toISOString(),
            ],
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function normalizeViolations(array $violations): array
    {
        $norm = [];
        
        foreach ($violations as $v) {
            $nodes = [];
            foreach ($v['nodes'] ?? [] as $n) {
                $nodes[] = [
                    'html'      => $n['html']      ?? '',
                    'target'    => (array)($n['target'] ?? []),
                    'failure'   => $n['failureSummary'] ?? '',
                ];
            }

            $norm[] = [
                'id'        => $v['id']        ?? '',
                'impact'    => $v['impact']    ?? 'minor',
                'help'      => $v['help']      ?? '',
                'desc'      => $v['description'] ?? '',
                'helpUrl'   => $v['helpUrl']   ?? '',
                'tags'      => (array)($v['tags'] ?? []),
                'nodes'     => $nodes,
            ];
        }

        // impact 순으로 정렬
        usort($norm, function ($a, $b) {
            $order = ['critical' => 0, 'serious' => 1, 'moderate' => 2, 'minor' => 3];
            return ($order[$a['impact']] ?? 9) <=> ($order[$b['impact']] ?? 9);
        });

        return $norm;
    }

    private function calculateGrade(array $counts): string
    {
        $crit = $counts['critical'] ?? 0;
        $ser = $counts['serious'] ?? 0;
        $mod = $counts['moderate'] ?? 0;
        $min = $counts['minor'] ?? 0;
        $total = $counts['total'] ?? 0;

        // 점수 기반 등급 산정 (100점 만점 기준)
        $score = $this->calculateScore($counts);

        // A+ : 90점 이상 + critical=0, serious=0, total ≤ 3
        if ($score >= 90 && $crit === 0 && $ser === 0 && $total <= 3) {
            return 'A+';
        }

        // A  : 80점 이상 + critical=0, serious ≤ 3, total ≤ 8
        if ($score >= 80 && $crit === 0 && $ser <= 3 && $total <= 8) {
            return 'A';
        }

        // B  : 70점 이상 + critical ≤ 1, serious ≤ 6, total ≤ 15
        if ($score >= 70 && $crit <= 1 && $ser <= 6 && $total <= 15) {
            return 'B';
        }

        // C  : 60점 이상 + critical ≤ 3, serious ≤ 10, total ≤ 25
        if ($score >= 60 && $crit <= 3 && $ser <= 10 && $total <= 25) {
            return 'C';
        }

        // D  : 50점 이상 + (critical ≤ 6 또는 serious ≤ 18), total ≤ 40
        if ($score >= 50 && ($crit <= 6 || $ser <= 18) && $total <= 40) {
            return 'D';
        }

        // F  : 그 외
        return 'F';
    }

    private function calculateScore(array $counts): float
    {
        $crit = $counts['critical'] ?? 0;
        $ser = $counts['serious'] ?? 0;
        $mod = $counts['moderate'] ?? 0;
        $min = $counts['minor'] ?? 0;

        // 감점 계산
        $deduction = ($crit * $this->W_CRIT) + 
                     ($ser * $this->W_SER) + 
                     ($mod * $this->W_MOD) + 
                     ($min * $this->W_MIN);

        $score = max(0, $this->SCORE_MAX - $deduction);
        
        return round($score, 1);
    }
}