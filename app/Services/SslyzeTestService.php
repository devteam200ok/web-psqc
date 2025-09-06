<?php

namespace App\Services;

use App\Models\WebTest;
use App\Validators\UrlSecurityValidator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SslyzeTestService
{
    public function runTest($url, $testId)
    {
        $test = WebTest::find($testId);
        
        if (!$test) {
            throw new \Exception('Test not found with ID: ' . $testId);
        }
        
        try {
            // 보안 검증
            $securityErrors = UrlSecurityValidator::validateWithDnsCheck($url);
            if (!empty($securityErrors)) {
                throw new \Exception('보안 검증 실패: ' . implode(', ', $securityErrors));
            }
            
            // 테스트 상태를 running으로 업데이트
            $test->update(['status' => 'running']);
            
            // SSLyze 테스트 실행
            $results = $this->performSslyzeTest($url);
            
            // 결과 파싱 및 저장
            $this->parseAndSaveResults($test, $results);
            
        } catch (\Exception $e) {
            $test->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    private function performSslyzeTest($url): array
    {
        $disk = Storage::disk('local');
        $dir = 'sslyze';
        $disk->makeDirectory($dir);
        
        $relativePath = $dir . '/sslyze-' . Str::uuid() . '.json';
        $absolutePath = $disk->path($relativePath);
        
        // URL에서 host와 port 추출
        $parts = parse_url(trim($url));
        if (!$parts || empty($parts['host'])) {
            throw new \InvalidArgumentException('유효한 URL을 입력하세요.');
        }
        $host = $parts['host'];
        $port = $parts['port'] ?? 443;
        $target = $host . ':' . $port;
        
        // sslyze 실행
        $cmd = [
            'sslyze',
            "--json_out={$absolutePath}",
            '--tlsv1_2',
            '--tlsv1_3',
            '--http_headers',
            '--certinfo',
            '--elliptic_curves',
            $target,
        ];
        
        $env = [
            'PYTHONWARNINGS' => 'ignore',
            'LC_ALL' => 'C',
            'LANG' => 'C',
        ];
        
        $process = new Process($cmd, base_path(), $env, null, 180);
        $process->run();
        
        if (!$process->isSuccessful()) {
            $stderr = trim($process->getErrorOutput());
            $stdout = trim($process->getOutput());
            throw new \RuntimeException($stderr !== '' ? $stderr : ($stdout !== '' ? $stdout : 'sslyze 실행 실패'));
        }
        
        // 결과 읽기
        $json = null;
        if ($disk->exists($relativePath)) {
            $json = $disk->get($relativePath);
        } elseif (is_file($absolutePath)) {
            $json = file_get_contents($absolutePath);
        } else {
            throw new \RuntimeException("결과 파일을 찾을 수 없습니다: {$relativePath}");
        }
        
        if (empty($json)) {
            throw new \RuntimeException('빈 JSON 파일이 생성되었습니다.');
        }
        
        return [
            'json' => $json,
            'file_path' => $relativePath,
            'absolute_path' => $absolutePath
        ];
    }
    
    /**
     * JSON 인코딩 가능한 값으로 정리
     */
    private function sanitizeForJson($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeForJson'], $data);
        }
        
        if (is_object($data)) {
            $array = (array) $data;
            return $this->sanitizeForJson($array);
        }
        
        if (is_float($data)) {
            if (is_infinite($data)) {
                return $data > 0 ? 'Infinity' : '-Infinity';
            }
            if (is_nan($data)) {
                return 'NaN';
            }
        }
        
        return $data;
    }
    
    private function parseAndSaveResults($test, $results)
    {
        $json = $results['json'];
        $data = json_decode($json, true);
        
        if (!$data || !isset($data['server_scan_results'][0])) {
            throw new \Exception('JSON 파싱 실패 또는 빈 결과');
        }
        
        $result = $data['server_scan_results'][0];
        $scan = $result['scan_result'] ?? [];
        
        // 상세 분석
        $analysis = $this->performDetailedAnalysis($scan);
        
        // 메트릭 추출 (WebTest metrics 필드용)
        $metrics = $this->extractMetrics($analysis);
        
        // 등급 및 점수 계산
        $gradeData = $this->calculateGradeAndScore($analysis);
        
        // JSON 인코딩을 위한 데이터 정리
        $sanitizedAnalysis = $this->sanitizeForJson($analysis);
        $sanitizedMetrics = $this->sanitizeForJson($metrics);
        $sanitizedIssues = $this->sanitizeForJson($gradeData['issues']);
        $sanitizedRecommendations = $this->sanitizeForJson($gradeData['recommendations']);
        
        // raw_json은 원본 데이터가 너무 클 수 있으므로 필요한 부분만 저장
        $sanitizedRawJson = $this->sanitizeRawJson($data);
        
        // WebTest 업데이트
        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $gradeData['grade'],
            'overall_score' => $gradeData['score'],
            'metrics' => $sanitizedMetrics,
            'results' => [
                'analysis' => $sanitizedAnalysis,
                'issues' => $sanitizedIssues,
                'recommendations' => $sanitizedRecommendations,
                'raw_json' => $sanitizedRawJson,
                'file_path' => $results['file_path'],
                'tested_at' => now()->toISOString(),
            ]
        ]);
        
        // 사용자별 테스트 정리
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }
    
    /**
     * Raw JSON 데이터에서 필요한 부분만 추출하고 정리
     */
    private function sanitizeRawJson($data)
    {
        // 기본 정보만 유지
        $sanitized = [
            'server_info' => $data['server_info'] ?? null,
            'scan_status' => $data['server_scan_results'][0]['scan_status'] ?? null,
            'connectivity_status' => $data['server_scan_results'][0]['server_location']['connectivity_status'] ?? null,
        ];
        
        // 주요 스캔 결과 요약
        if (isset($data['server_scan_results'][0]['scan_result'])) {
            $scanResult = $data['server_scan_results'][0]['scan_result'];
            $sanitized['scan_summary'] = [
                'tls_1_2_supported' => $scanResult['tls_1_2_cipher_suites']['result']['is_tls_version_supported'] ?? false,
                'tls_1_3_supported' => $scanResult['tls_1_3_cipher_suites']['result']['is_tls_version_supported'] ?? false,
                'certificate_hostname_validation' => $scanResult['certificate_info']['result']['hostname_validation'][0]['result'] ?? null,
            ];
        }
        
        return $this->sanitizeForJson($sanitized);
    }
    
    private function performDetailedAnalysis(array $scan): array
    {
        return [
            'tls_versions' => $this->analyzeTlsVersions($scan),
            'cipher_suites' => $this->analyzeCipherSuites($scan),
            'certificate' => $this->analyzeCertificate($scan),
            'ocsp' => $this->analyzeOcsp($scan),
            'http_headers' => $this->analyzeHttpHeaders($scan),
            'elliptic_curves' => $this->analyzeEllipticCurves($scan),
        ];
    }
    
    private function analyzeTlsVersions(array $scan): array
    {
        $versions = [];
        $issues = [];
        
        // TLS 1.2
        if (isset($scan['tls_1_2_cipher_suites']['result']['is_tls_version_supported'])) {
            $versions['tls_1_2'] = $scan['tls_1_2_cipher_suites']['result']['is_tls_version_supported'];
        }
        
        // TLS 1.3
        if (isset($scan['tls_1_3_cipher_suites']['result']['is_tls_version_supported'])) {
            $versions['tls_1_3'] = $scan['tls_1_3_cipher_suites']['result']['is_tls_version_supported'];
        }
        
        // 구식 버전 확인
        $oldVersions = ['ssl_2_0_cipher_suites', 'ssl_3_0_cipher_suites', 'tls_1_0_cipher_suites', 'tls_1_1_cipher_suites'];
        foreach ($oldVersions as $version) {
            if (isset($scan[$version]['result']['is_tls_version_supported']) && 
                $scan[$version]['result']['is_tls_version_supported']) {
                $versionName = str_replace('_cipher_suites', '', strtoupper(str_replace('_', ' ', $version)));
                $issues[] = "구식 프로토콜 {$versionName} 지원됨";
            }
        }
        
        if (!($versions['tls_1_2'] ?? false) && !($versions['tls_1_3'] ?? false)) {
            $issues[] = '현대적 TLS 버전(1.2/1.3) 미지원';
        }
        
        if (!($versions['tls_1_3'] ?? false)) {
            $issues[] = 'TLS 1.3 미지원';
        }
        
        return [
            'supported_versions' => $versions,
            'issues' => $issues
        ];
    }
    
    private function analyzeCipherSuites(array $scan): array
    {
        $analysis = [
            'tls_1_2' => ['total' => 0, 'strong' => 0, 'weak' => 0, 'pfs_count' => 0, 'issues' => []],
            'tls_1_3' => ['total' => 0, 'ciphers' => []],
            'weak_ciphers' => []
        ];
        
        // TLS 1.2 암호군 분석
        if (isset($scan['tls_1_2_cipher_suites']['result']['accepted_cipher_suites'])) {
            $ciphers = $scan['tls_1_2_cipher_suites']['result']['accepted_cipher_suites'];
            $analysis['tls_1_2'] = $this->analyzeTls12Ciphers($ciphers);
        }
        
        // TLS 1.3 암호군 분석
        if (isset($scan['tls_1_3_cipher_suites']['result']['accepted_cipher_suites'])) {
            $ciphers = $scan['tls_1_3_cipher_suites']['result']['accepted_cipher_suites'];
            $analysis['tls_1_3'] = [
                'total' => count($ciphers),
                'ciphers' => array_map(fn($c) => $c['cipher_suite']['name'] ?? 'Unknown', $ciphers)
            ];
        }
        
        return $analysis;
    }
    
    private function analyzeTls12Ciphers(array $ciphers): array
    {
        $strong = 0;
        $weak = 0;
        $pfs_count = 0;
        $issues = [];
        $weak_ciphers = [];
        
        foreach ($ciphers as $cipher) {
            $name = $cipher['cipher_suite']['name'] ?? '';
            $keySize = $cipher['cipher_suite']['key_size'] ?? 0;
            
            // PFS 확인
            if (strpos($name, 'ECDHE') !== false || strpos($name, 'DHE') !== false) {
                $pfs_count++;
            }
            
            // 약한 암호 확인
            if (strpos($name, 'RC4') !== false || strpos($name, 'DES') !== false || 
                strpos($name, 'NULL') !== false || strpos($name, 'EXPORT') !== false || 
                strpos($name, 'anon') !== false || $keySize < 128) {
                $weak++;
                $weak_ciphers[] = $name;
                if (count($issues) < 3) { // 최대 3개만 표시
                    $issues[] = "약한 암호군: {$name}";
                }
            } elseif (strpos($name, 'CBC') !== false && strpos($name, 'SHA1') !== false) {
                // CBC with SHA1은 경고 수준
                $issues[] = "구식 암호군: {$name}";
            } else {
                $strong++;
            }
        }
        
        $total = count($ciphers);
        $pfs_ratio = $total > 0 ? round(($pfs_count / $total) * 100, 1) : 0;
        
        if ($weak > 0) {
            $issues[] = "총 {$weak}개의 약한 암호군 감지";
        }
        
        if ($pfs_ratio < 100) {
            $issues[] = "PFS 지원율 {$pfs_ratio}% (100% 권장)";
        }
        
        return [
            'total' => $total,
            'strong' => $strong,
            'weak' => $weak,
            'pfs_count' => $pfs_count,
            'pfs_ratio' => $pfs_ratio,
            'issues' => $issues,
            'weak_ciphers' => $weak_ciphers
        ];
    }
    
    private function analyzeCertificate(array $scan): array
    {
        $analysis = ['issues' => [], 'details' => []];
        
        if (!isset($scan['certificate_info']['result']['certificate_deployments'][0])) {
            $analysis['issues'][] = '인증서 정보를 찾을 수 없음';
            return $analysis;
        }
        
        $deployment = $scan['certificate_info']['result']['certificate_deployments'][0];
        $cert = $deployment['received_certificate_chain'][0] ?? null;
        
        if (!$cert) {
            $analysis['issues'][] = '인증서 체인 정보 없음';
            return $analysis;
        }
        
        // 만료일 확인
        $notAfter = $cert['not_valid_after'] ?? '';
        if ($notAfter) {
            $expiryDate = new \DateTime($notAfter);
            $now = new \DateTime();
            $diff = $now->diff($expiryDate);
            $daysToExpiry = $diff->invert ? -$diff->days : $diff->days;
            
            $analysis['details']['days_to_expiry'] = $daysToExpiry;
            
            if ($daysToExpiry <= 0) {
                $analysis['issues'][] = "인증서 만료됨";
            } elseif ($daysToExpiry <= 14) {
                $analysis['issues'][] = "인증서 만료 임박 ({$daysToExpiry}일 남음)";
            } elseif ($daysToExpiry <= 30) {
                $analysis['issues'][] = "인증서 갱신 권장 ({$daysToExpiry}일 남음)";
            }
        }
        
        // 공개키 분석
        $keyAlgo = $cert['public_key']['algorithm'] ?? '';
        $keySize = $cert['public_key']['key_size'] ?? 0;
        
        $analysis['details']['key_algorithm'] = $keyAlgo;
        $analysis['details']['key_size'] = $keySize;
        
        if ($keyAlgo === 'RSAPublicKey') {
            if ($keySize < 2048) {
                $analysis['issues'][] = "RSA 키 크기 부족 ({$keySize}비트, 최소 2048비트 필요)";
            } elseif ($keySize < 3072) {
                $analysis['issues'][] = "RSA 키 크기 권장 미달 ({$keySize}비트, 3072비트 권장)";
            }
        }
        
        // 서명 알고리즘
        $sigAlgo = $cert['signature_hash_algorithm']['name'] ?? '';
        $analysis['details']['signature_algorithm'] = $sigAlgo;
        
        if (strpos(strtolower($sigAlgo), 'sha1') !== false || strpos(strtolower($sigAlgo), 'md5') !== false) {
            $analysis['issues'][] = "약한 서명 알고리즘 사용: {$sigAlgo}";
        }
        
        // 인증서 체인 검증
        $chainLength = count($deployment['received_certificate_chain'] ?? []);
        $analysis['details']['chain_length'] = $chainLength;
        
        if ($chainLength < 2) {
            $analysis['issues'][] = '인증서 체인 불완전 (중간 인증서 누락 가능성)';
        }
        
        return $analysis;
    }
    
    private function analyzeOcsp(array $scan): array
    {
        $analysis = ['status' => 'unknown', 'issues' => []];
        
        if (isset($scan['certificate_info']['result']['certificate_deployments'][0]['ocsp_response'])) {
            $ocsp = $scan['certificate_info']['result']['certificate_deployments'][0]['ocsp_response'];
            $status = $ocsp['response_status'] ?? '';
            $certStatus = $ocsp['certificate_status'] ?? '';
            
            $analysis['status'] = $status;
            $analysis['certificate_status'] = $certStatus;
            
            if ($status === 'SUCCESSFUL' && $certStatus === 'GOOD') {
                // OCSP Stapling 정상
            } elseif ($status !== 'SUCCESSFUL') {
                $analysis['issues'][] = 'OCSP Stapling 비활성 또는 실패';
            } else {
                $analysis['issues'][] = "인증서 상태 이상: {$certStatus}";
            }
        } else {
            $analysis['issues'][] = 'OCSP Stapling 미구성';
        }
        
        return $analysis;
    }
    
    private function analyzeHttpHeaders(array $scan): array
    {
        $analysis = ['hsts' => null, 'issues' => []];
        
        if (isset($scan['http_headers']['result']['strict_transport_security_header'])) {
            $hsts = $scan['http_headers']['result']['strict_transport_security_header'];
            $analysis['hsts'] = $hsts;
            
            $maxAge = $hsts['max_age'] ?? 0;
            if ($maxAge < 15552000) { // 6개월
                $analysis['issues'][] = 'HSTS max-age 값이 낮음 (최소 6개월 권장)';
            }
            
            if (!($hsts['include_subdomains'] ?? false)) {
                $analysis['issues'][] = 'HSTS includeSubDomains 미설정';
            }
            
            if (!($hsts['preload'] ?? false)) {
                $analysis['issues'][] = 'HSTS preload 미설정';
            }
        } else {
            $analysis['issues'][] = 'HSTS 헤더 미설정';
        }
        
        return $analysis;
    }
    
    private function analyzeEllipticCurves(array $scan): array
    {
        $analysis = ['supported' => [], 'issues' => []];
        
        if (isset($scan['elliptic_curves']['result']['supported_curves'])) {
            $curves = $scan['elliptic_curves']['result']['supported_curves'];
            $analysis['supported'] = array_column($curves, 'name');
            
            $modern_curves = ['X25519', 'secp256r1', 'secp384r1', 'secp521r1'];
            $has_modern = array_intersect($modern_curves, $analysis['supported']);
            
            if (empty($has_modern)) {
                $analysis['issues'][] = '현대적 타원곡선 미지원';
            }
            
            // 약한 곡선 체크
            $weak_curves = ['secp192r1', 'secp224r1'];
            $has_weak = array_intersect($weak_curves, $analysis['supported']);
            if (!empty($has_weak)) {
                $analysis['issues'][] = '약한 타원곡선 지원: ' . implode(', ', $has_weak);
            }
        }
        
        return $analysis;
    }
    
    private function extractMetrics(array $analysis): array
    {
        $metrics = [];
        
        // TLS 버전 메트릭
        $metrics['tls_1_2_supported'] = $analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false;
        $metrics['tls_1_3_supported'] = $analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false;
        
        // 암호군 메트릭
        if (isset($analysis['cipher_suites']['tls_1_2'])) {
            $metrics['cipher_total'] = $analysis['cipher_suites']['tls_1_2']['total'];
            $metrics['cipher_weak'] = $analysis['cipher_suites']['tls_1_2']['weak'];
            $metrics['pfs_ratio'] = $analysis['cipher_suites']['tls_1_2']['pfs_ratio'];
        }
        
        // 인증서 메트릭
        if (isset($analysis['certificate']['details'])) {
            $metrics['cert_days_to_expiry'] = $analysis['certificate']['details']['days_to_expiry'] ?? null;
            $metrics['cert_key_size'] = $analysis['certificate']['details']['key_size'] ?? null;
            $metrics['cert_key_algo'] = $analysis['certificate']['details']['key_algorithm'] ?? null;
        }
        
        // OCSP 메트릭
        $metrics['ocsp_stapling'] = ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL';
        
        // HSTS 메트릭
        $metrics['hsts_enabled'] = isset($analysis['http_headers']['hsts']);
        if ($metrics['hsts_enabled']) {
            $metrics['hsts_max_age'] = $analysis['http_headers']['hsts']['max_age'] ?? 0;
        }
        
        return $metrics;
    }
    
    private function calculateGradeAndScore(array $analysis): array
    {
        $issues = [];
        $recommendations = [];
        $score = 0;
        $maxScore = 100;
        
        // 1. TLS 버전 평가 (25점)
        $tlsScore = 0;
        $tls12 = $analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false;
        $tls13 = $analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false;
        
        if ($tls13 && $tls12) {
            $tlsScore = 25; // TLS 1.3과 1.2 모두 지원
        } elseif ($tls12) {
            $tlsScore = 15; // TLS 1.2만 지원
        }
        
        // 구식 프로토콜 감점
        $oldProtocolCount = count($analysis['tls_versions']['issues']);
        $tlsScore -= min($oldProtocolCount * 5, 15);
        
        $score += max(0, $tlsScore);
        
        // 2. 암호군 평가 (25점)
        $cipherScore = 0;
        if (isset($analysis['cipher_suites']['tls_1_2'])) {
            $tls12Ciphers = $analysis['cipher_suites']['tls_1_2'];
            
            // PFS 비율 (10점)
            $pfsRatio = $tls12Ciphers['pfs_ratio'] ?? 0;
            $cipherScore += ($pfsRatio / 100) * 10;
            
            // 약한 암호 감점
            $weakCount = $tls12Ciphers['weak'] ?? 0;
            if ($weakCount === 0) {
                $cipherScore += 10; // 약한 암호 없음
            } else {
                $cipherScore -= min($weakCount * 3, 15);
            }
            
            // TLS 1.3 암호군 보너스
            if (($analysis['cipher_suites']['tls_1_3']['total'] ?? 0) > 0) {
                $cipherScore += 5;
            }
        }
        
        $score += max(0, $cipherScore);
        
        // 3. 인증서 평가 (20점)
        $certScore = 20;
        $certIssues = $analysis['certificate']['issues'] ?? [];
        
        foreach ($certIssues as $issue) {
            if (strpos($issue, '만료') !== false) {
                if (strpos($issue, '만료됨') !== false) {
                    $certScore = 0; // 만료된 인증서
                } elseif (strpos($issue, '임박') !== false) {
                    $certScore -= 10;
                } else {
                    $certScore -= 5;
                }
            } elseif (strpos($issue, '키 크기') !== false) {
                $certScore -= 5;
            } elseif (strpos($issue, '약한 서명') !== false) {
                $certScore -= 8;
            } elseif (strpos($issue, '체인') !== false) {
                $certScore -= 5;
            }
        }
        
        // 키 알고리즘 보너스
        $keyAlgo = $analysis['certificate']['details']['key_algorithm'] ?? '';
        $keySize = $analysis['certificate']['details']['key_size'] ?? 0;
        
        if ($keyAlgo === 'ECPublicKey' && $keySize >= 256) {
            $certScore += 3; // ECDSA 보너스
        } elseif ($keyAlgo === 'RSAPublicKey' && $keySize >= 3072) {
            $certScore += 2; // RSA 3072+ 보너스
        }
        
        $score += max(0, min(20, $certScore));
        
        // 4. OCSP Stapling 평가 (10점)
        if (($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' && 
            ($analysis['ocsp']['certificate_status'] ?? '') === 'GOOD') {
            $score += 10;
        } elseif (empty($analysis['ocsp']['issues'])) {
            $score += 5; // 부분 지원
        }
        
        // 5. HTTP 보안 헤더 평가 (10점)
        if (isset($analysis['http_headers']['hsts'])) {
            $hstsScore = 5; // 기본 HSTS
            $hsts = $analysis['http_headers']['hsts'];
            
            if (($hsts['max_age'] ?? 0) >= 15552000) {
                $hstsScore += 2;
            }
            if ($hsts['include_subdomains'] ?? false) {
                $hstsScore += 2;
            }
            if ($hsts['preload'] ?? false) {
                $hstsScore += 1;
            }
            
            $score += $hstsScore;
        }
        
        // 6. 타원곡선 평가 (10점)
        if (!empty($analysis['elliptic_curves']['supported'])) {
            $curveScore = 5;
            $modern_curves = ['X25519', 'secp256r1', 'secp384r1'];
            $supported = $analysis['elliptic_curves']['supported'];
            $has_modern = array_intersect($modern_curves, $supported);
            
            if (count($has_modern) >= 2) {
                $curveScore += 5;
            } elseif (count($has_modern) >= 1) {
                $curveScore += 3;
            }
            
            // 약한 곡선 감점
            if (!empty($analysis['elliptic_curves']['issues'])) {
                $curveScore -= 3;
            }
            
            $score += max(0, $curveScore);
        }
        
        // 이슈 수집
        foreach ($analysis as $category => $data) {
            if (isset($data['issues'])) {
                $issues = array_merge($issues, $data['issues']);
            }
        }
        
        // 권장사항 생성
        $recommendations = $this->generateRecommendations($analysis);
        
        // 등급 계산
        $grade = $this->determineGrade($score);
        
        return [
            'grade' => $grade,
            'score' => round($score),
            'issues' => array_unique($issues),
            'recommendations' => $recommendations
        ];
    }
    
    private function determineGrade(float $score): string
    {
        // 첨부된 등급 기준에 맞춰 조정
        if ($score >= 90) {
            return 'A+';
        } elseif ($score >= 80) {
            return 'A';
        } elseif ($score >= 65) {
            return 'B';
        } elseif ($score >= 50) {
            return 'C';
        } elseif ($score >= 35) {
            return 'D';
        } else {
            return 'F';
        }
    }
    
    private function generateRecommendations(array $analysis): array
    {
        $recommendations = [];
        
        // TLS 권장사항
        if (!($analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false)) {
            $recommendations[] = 'TLS 1.3을 활성화하여 최신 보안 표준을 준수하세요.';
        }
        
        if (!empty($analysis['tls_versions']['issues'])) {
            foreach ($analysis['tls_versions']['issues'] as $issue) {
                if (strpos($issue, '구식 프로토콜') !== false) {
                    $recommendations[] = '구식 프로토콜(SSL 3.0, TLS 1.0/1.1)을 비활성화하세요.';
                    break;
                }
            }
        }
        
        // 암호군 권장사항
        if (isset($analysis['cipher_suites']['tls_1_2'])) {
            $pfsRatio = $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0;
            if ($pfsRatio < 100) {
                $recommendations[] = '모든 암호군에서 Perfect Forward Secrecy(PFS)를 지원하도록 설정하세요.';
            }
            
            if (($analysis['cipher_suites']['tls_1_2']['weak'] ?? 0) > 0) {
                $recommendations[] = '약한 암호군(RC4, DES, NULL, EXPORT)을 비활성화하세요.';
            }
        }
        
        // 인증서 권장사항
        if (!empty($analysis['certificate']['details'])) {
            $keyAlgo = $analysis['certificate']['details']['key_algorithm'] ?? '';
            $keySize = $analysis['certificate']['details']['key_size'] ?? 0;
            
            if ($keyAlgo === 'RSAPublicKey' && $keySize < 3072) {
                $recommendations[] = 'RSA 인증서의 키 크기를 3072비트 이상으로 업그레이드하거나 ECDSA 인증서로 전환을 고려하세요.';
            }
            
            $daysToExpiry = $analysis['certificate']['details']['days_to_expiry'] ?? 0;
            if ($daysToExpiry <= 30 && $daysToExpiry > 0) {
                $recommendations[] = '인증서 만료가 가까워지고 있습니다. 갱신을 준비하세요.';
            }
        }
        
        // OCSP 권장사항
        if (($analysis['ocsp']['status'] ?? '') !== 'SUCCESSFUL') {
            $recommendations[] = 'OCSP Stapling을 활성화하여 인증서 상태 확인 성능을 개선하세요.';
        }
        
        // HSTS 권장사항
        if (!isset($analysis['http_headers']['hsts'])) {
            $recommendations[] = 'HSTS(HTTP Strict Transport Security) 헤더를 설정하세요.';
        } else {
            $hsts = $analysis['http_headers']['hsts'];
            if (($hsts['max_age'] ?? 0) < 31536000) {
                $recommendations[] = 'HSTS max-age를 최소 1년(31536000초)로 설정하세요.';
            }
            if (!($hsts['include_subdomains'] ?? false)) {
                $recommendations[] = 'HSTS에 includeSubDomains 지시어를 추가하세요.';
            }
            if (!($hsts['preload'] ?? false)) {
                $recommendations[] = 'HSTS Preload 목록 등록을 고려하세요.';
            }
        }
        
        // 타원곡선 권장사항
        if (!empty($analysis['elliptic_curves']['issues'])) {
            $recommendations[] = '현대적 타원곡선(X25519, secp256r1, secp384r1)을 지원하세요.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = '현재 SSL/TLS 설정이 우수합니다. 정기적인 모니터링을 지속하세요.';
        }
        
        return array_unique($recommendations);
    }
}