<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use App\Validators\UrlSecurityValidator;
use Symfony\Component\Process\Process;

class HeadersTestService
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

            $results = $this->performHeadersTest($url);
            
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

    private function performHeadersTest($url): array
    {
        $cmd = ['node', base_path('scripts/security-headers-audit.mjs'), "--url={$url}"];
        $env = [];

        $process = new Process($cmd, base_path(), $env, null, 60);
        
        try {
            $process->mustRun();
            $json = $process->getOutput();
            $report = json_decode($json, true);
            
            if (!$report) {
                throw new \Exception('JSON 파싱 실패');
            }
            
            return $report;
            
        } catch (\Exception $e) {
            Log::error('Headers test failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'output' => $process->getOutput(),
                'error_output' => $process->getErrorOutput()
            ]);
            throw new \Exception('헤더 검사 실패: ' . $e->getMessage());
        }
    }

    private function parseAndSaveResults($test, $report)
    {
        // 메트릭 추출
        $metrics = $this->extractMetrics($report);
        
        // 등급 및 점수 계산 (60점 -> 100점 변환)
        $gradeData = $this->calculateGradeAndScore($report);
        
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $gradeData['grade'],
            'overall_score' => $gradeData['score'],
            'results' => $report,
            'metrics' => $metrics,
            'is_certified' => in_array($gradeData['grade'], ['A+', 'A', 'B', 'C'])
        ]);

        // 사용자별 테스트 정리
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics(array $report): array
    {
        $checks = $report['checks'] ?? [];
        
        // 원본 60점 만점 점수를 100점으로 변환
        $originalScore = $report['score'] ?? 0;
        $convertedScore = round(($originalScore / 60) * 100, 1);
        
        return [
            'total_score' => $convertedScore,
            'original_score' => $originalScore,
            'max_score' => 100,
            'headers' => [
                'csp' => [
                    'present' => $checks['csp']['present'] ?? false,
                    'strong' => $checks['csp']['strong'] ?? false,
                    'score' => $checks['csp']['score'] ?? 0
                ],
                'x_frame_options' => [
                    'present' => $checks['xFrameOptions']['present'] ?? false,
                    'value' => $checks['xFrameOptions']['value'] ?? null,
                    'score' => $checks['xFrameOptions']['score'] ?? 0
                ],
                'x_content_type' => [
                    'present' => $checks['xContentTypeOptions']['present'] ?? false,
                    'value' => $checks['xContentTypeOptions']['value'] ?? null,
                    'score' => $checks['xContentTypeOptions']['score'] ?? 0
                ],
                'referrer_policy' => [
                    'present' => $checks['referrerPolicy']['present'] ?? false,
                    'value' => $checks['referrerPolicy']['value'] ?? null,
                    'score' => $checks['referrerPolicy']['score'] ?? 0
                ],
                'permissions_policy' => [
                    'present' => $checks['permissionsPolicy']['present'] ?? false,
                    'score' => $checks['permissionsPolicy']['score'] ?? 0
                ],
                'hsts' => [
                    'present' => $checks['hsts']['present'] ?? false,
                    'max_age' => $checks['hsts']['maxAge'] ?? 0,
                    'include_sub_domains' => $checks['hsts']['includeSubDomains'] ?? false,
                    'score' => $checks['hsts']['score'] ?? 0
                ]
            ],
            'breakdown' => $report['breakdown'] ?? []
        ];
    }

    private function calculateGradeAndScore(array $report): array
    {
        $originalScore = $report['score'] ?? 0;
        $checks = $report['checks'] ?? [];
        
        // 60점 만점을 100점 만점으로 변환
        $score = round(($originalScore / 60) * 100, 1);
        
        // CSP 체크
        $csp = $checks['csp'] ?? [];
        $hasStrongCSP = ($csp['present'] ?? false) && ($csp['strong'] ?? false);
        $hasWeakCSP = ($csp['present'] ?? false) && !($csp['strong'] ?? false);
        
        // 비-CSP 5항목 체크 (XFO, XCTO, Referrer, Permissions, HSTS)
        $nonCSPHeaders = ['xFrameOptions', 'xContentTypeOptions', 'referrerPolicy', 'permissionsPolicy', 'hsts'];
        $excellentNonCSPCount = 0;
        
        foreach ($nonCSPHeaders as $header) {
            if (isset($checks[$header])) {
                $headerCheck = $checks[$header];
                
                // 각 헤더별 우수 기준 체크
                $isExcellent = false;
                
                switch($header) {
                    case 'xFrameOptions':
                        $isExcellent = ($headerCheck['present'] ?? false) && 
                                     in_array($headerCheck['value'] ?? '', ['DENY', 'SAMEORIGIN']);
                        break;
                    
                    case 'xContentTypeOptions':
                        $isExcellent = ($headerCheck['present'] ?? false) && 
                                     ($headerCheck['value'] ?? '') === 'nosniff';
                        break;
                    
                    case 'referrerPolicy':
                        $isExcellent = ($headerCheck['present'] ?? false) && 
                                     in_array($headerCheck['value'] ?? '', [
                                         'strict-origin-when-cross-origin',
                                         'strict-origin',
                                         'no-referrer'
                                     ]);
                        break;
                    
                    case 'permissionsPolicy':
                        $isExcellent = ($headerCheck['present'] ?? false) && 
                                     ($headerCheck['score'] ?? 0) >= 9;
                        break;
                    
                    case 'hsts':
                        $sixMonths = 15552000;
                        $isExcellent = ($headerCheck['present'] ?? false) && 
                                     ($headerCheck['maxAge'] ?? 0) >= $sixMonths &&
                                     ($headerCheck['includeSubDomains'] ?? false);
                        break;
                }
                
                if ($isExcellent) {
                    $excellentNonCSPCount++;
                }
            }
        }
        
        $allNonCSPExcellent = ($excellentNonCSPCount === 5);
        
        // 등급 계산 (100점 기준으로 변경)
        $grade = 'F';
        
        if ($score >= 95 && $hasStrongCSP) {
            $grade = 'A+';
        } elseif ($score >= 85 || $allNonCSPExcellent) {
            $grade = 'A';
        } elseif ($score >= 70) {
            $grade = 'B';
        } elseif ($score >= 55) {
            $grade = 'C';
        } elseif ($score >= 40) {
            $grade = 'D';
        } else {
            $grade = 'F';
        }
        
        return [
            'grade' => $grade,
            'score' => $score
        ];
    }
}