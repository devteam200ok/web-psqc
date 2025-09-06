<?php

namespace App\Services;

use App\Models\WebTest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use App\Validators\UrlSecurityValidator;

class SslTestService
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

            $results = $this->performSslTest($url);
            
            if (empty($results)) {
                throw new \Exception('SSL test failed to produce results');
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
            
            Log::error('SSL test failed: ' . $e->getMessage(), [
                'test_id' => $testId,
                'url' => $url,
                'error' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    private function performSslTest($url): array
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? null;
        $port = $parsedUrl['port'] ?? 443;
        
        if (!$host) {
            throw new \Exception('Invalid URL format');
        }

        $testsslPath = $this->findTestSslPath();
        
        // Use text mode directly with better parsing
        $command = sprintf(
            '%s --warnings off --openssl-timeout 10 --fast %s:%s',
            escapeshellarg($testsslPath),
            escapeshellarg($host),
            escapeshellarg($port)
        );

        $result = Process::timeout(600)->run($command);

        if (!$result->successful()) {
            throw new \Exception('testssl.sh failed: ' . $result->errorOutput());
        }

        return $this->parseTextOutput($result->output(), $host, $port);
    }

    private function parseTextOutput($output, $host, $port): array
    {
        // Strip ANSI color codes
        $cleanOutput = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
        
        $results = [
            'ip_address' => $host,
            'port' => $port,
            'scan_time' => date('Y-m-d H:i:s'),
            'server_banner' => '',
            'supported_protocols' => [],
            'vulnerable_protocols' => [],
            'cert_expiry' => null,
            'certificate' => [],
            'protocol_support' => [],
            'cipher_suites' => [],
            'vulnerabilities' => [],
            'security_headers' => [],
            'raw_output' => $output
        ];

        $lines = explode("\n", $cleanOutput);

        // Parse certificate information
        $this->parseCertificateInfo($lines, $results);
        
        // Parse protocol support
        $this->parseProtocolSupport($lines, $results);
        
        // Parse security headers
        $this->parseSecurityHeaders($lines, $results);
        
        // Parse vulnerabilities
        $this->parseVulnerabilities($lines, $results);
        
        // Parse cipher suites
        $this->parseCipherSuites($lines, $results);
        
        // Parse server information
        $this->parseServerInfo($lines, $results);

        return $results;
    }

    private function parseAndSaveResults($test, $rawResults)
    {
        // 메트릭 추출
        $metrics = $this->extractMetrics($rawResults);
        
        // 등급 계산
        $grade = $this->calculateGrade($rawResults);
        $score = $this->calculateScore($grade);
        
        // SSL 관련 추가 필드 추출
        $tlsVersion = $this->extractTlsVersion($rawResults);
        $forwardSecrecy = $this->checkForwardSecrecy($rawResults);
        $hstsEnabled = $this->checkHstsEnabled($rawResults);

        $test->update([
            'status' => 'completed',
            'finished_at' => now(),
            'overall_grade' => $grade,
            'overall_score' => $score,
            'results' => $rawResults,
            'metrics' => $metrics,
        ]);

        // 사용자별 테스트 정리 (로그인 사용자인 경우)
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }

    private function extractMetrics(array $rawResults): array
    {
        $metrics = [];
        
        // SSL 등급 및 기본 정보
        $metrics['ssl_grade'] = $this->calculateGrade($rawResults);
        $metrics['tls_version'] = $this->extractTlsVersion($rawResults);
        $metrics['forward_secrecy'] = $this->checkForwardSecrecy($rawResults);
        $metrics['hsts_enabled'] = $this->checkHstsEnabled($rawResults);
        
        // 프로토콜 지원 현황
        $metrics['supported_protocols_count'] = count($rawResults['supported_protocols'] ?? []);
        $metrics['vulnerable_protocols_count'] = count($rawResults['vulnerable_protocols'] ?? []);
        
        // 취약점 통계
        $vulnerableCount = 0;
        $criticalCount = 0;
        foreach ($rawResults['vulnerabilities'] ?? [] as $vuln) {
            if ($vuln['vulnerable'] ?? false) {
                $vulnerableCount++;
                if (($vuln['severity'] ?? '') === 'high') {
                    $criticalCount++;
                }
            }
        }
        $metrics['vulnerable_count'] = $vulnerableCount;
        $metrics['critical_vulnerable_count'] = $criticalCount;
        
        // 보안 헤더 점수
        $presentCount = 0;
        $totalHeaders = count($rawResults['security_headers'] ?? []);
        foreach ($rawResults['security_headers'] ?? [] as $header) {
            if ($header['present'] ?? false) {
                $presentCount++;
            }
        }
        $metrics['security_headers_score'] = $totalHeaders > 0 ? round(($presentCount / $totalHeaders) * 100) : 0;
        
        // 암호화 스위트 통계
        $metrics['cipher_suites_count'] = count($rawResults['cipher_suites'] ?? []);
        
        return $metrics;
    }

    private function calculateGrade($results): string
    {
        $score = 100;
        
        // TLS 버전 점수
        if (in_array('TLS 1.3', $results['supported_protocols'] ?? [])) {
            $score += 5;
        } elseif (!in_array('TLS 1.2', $results['supported_protocols'] ?? [])) {
            $score -= 20;
        }
        
        // 취약한 프로토콜 페널티
        foreach ($results['vulnerable_protocols'] ?? [] as $protocol) {
            if (strpos($protocol, 'SSL') !== false) {
                $score -= 30;
            } elseif (strpos($protocol, 'TLS 1.0') !== false || strpos($protocol, 'TLS 1.1') !== false) {
                $score -= 10;
            }
        }
        
        // 취약점 페널티
        foreach ($results['vulnerabilities'] ?? [] as $vuln) {
            if ($vuln['vulnerable'] ?? false) {
                $score -= ($vuln['severity'] ?? '') === 'high' ? 25 : 10;
            }
        }
        
        // 보안 기능 부족 페널티
        if (!$this->checkHstsEnabled($results)) {
            $score -= 5;
        }
        
        if (!$this->checkForwardSecrecy($results)) {
            $score -= 10;
        }
        
        // 등급 할당
        if ($score >= 95) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    private function calculateScore(string $grade): float
    {
        return match($grade) {
            'A+' => rand(90, 100),
            'A' => rand(80, 89),
            'B' => rand(70, 79),
            'C' => rand(60, 69),
            'D' => rand(50, 59),
            default => rand(0, 49)
        };
    }

    private function extractTlsVersion($results): string
    {
        if (in_array('TLS 1.3', $results['supported_protocols'] ?? [])) {
            return 'TLS 1.3';
        } elseif (in_array('TLS 1.2', $results['supported_protocols'] ?? [])) {
            return 'TLS 1.2';
        } elseif (in_array('TLS 1.1', $results['supported_protocols'] ?? [])) {
            return 'TLS 1.1';
        } elseif (in_array('TLS 1.0', $results['supported_protocols'] ?? [])) {
            return 'TLS 1.0';
        }
        return 'N/A';
    }

    private function checkForwardSecrecy($results): bool
    {
        foreach ($results['cipher_suites'] ?? [] as $cipher) {
            if (strpos($cipher['key_exchange'] ?? '', 'ECDHE') !== false) {
                return true;
            }
        }
        return false;
    }

    private function checkHstsEnabled($results): bool
    {
        return $results['security_headers']['HSTS']['present'] ?? false;
    }

    // testssl.sh 파싱 메소드들 (기존 코드 활용)
    private function parseCertificateInfo($lines, &$results)
    {
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Common Name
            if (preg_match('/Common Name \(CN\)\s+([^\s]+)/', $line, $matches)) {
                $cn = trim($matches[1]);
                $results['certificate']['common_name'] = $cn;
                $results['certificate']['subject'] = 'CN=' . $cn;
            }
            
            // Subject Alternative Name
            if (preg_match('/subjectAltName \(SAN\)\s+(.+)/', $line, $matches)) {
                $sanString = trim($matches[1]);
                $sans = preg_split('/\s+/', $sanString);
                $results['certificate']['san'] = array_filter($sans);
            }
            
            // Certificate validity
            if (preg_match('/Certificate Validity \(UTC\)\s+.+\(([^)]+)\s+-->\s+([^)]+)\)/', $line, $matches)) {
                $results['certificate']['valid_from'] = trim($matches[1]);
                $results['certificate']['valid_until'] = trim($matches[2]);
                $results['cert_expiry'] = trim($matches[2]);
            }
            
            // 기타 인증서 정보들...
        }
    }

    private function parseProtocolSupport($lines, &$results)
    {
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (preg_match('/^\s*(SSLv[23]|TLS\s*1(?:\.[0-3])?)\s+(.+)/', $line, $matches)) {
                $protocol = trim($matches[1]);
                $status = trim($matches[2]);
                
                $supported = (strpos(strtolower($status), 'offered') !== false && 
                            strpos(strtolower($status), 'not offered') === false);
                
                $protocolName = str_replace('  ', ' ', $protocol);
                
                $results['protocol_support'][$protocolName] = [
                    'supported' => $supported,
                    'security_level' => $this->getProtocolSecurityLevel($protocol)
                ];
                
                if ($supported) {
                    $results['supported_protocols'][] = $protocolName;
                    
                    // 취약한 프로토콜 체크
                    if (strpos($protocol, 'SSL') !== false || 
                        strpos($protocol, 'TLS 1.1') !== false || 
                        (strpos($protocol, 'TLS 1') === 0 && strpos($protocol, 'TLS 1.2') === false && strpos($protocol, 'TLS 1.3') === false)) {
                        $results['vulnerable_protocols'][] = $protocolName;
                    }
                }
            }
        }
    }

    private function parseSecurityHeaders($lines, &$results)
    {
        foreach ($lines as $line) {
            $line = trim($line);
            
            // HSTS
            if (preg_match('/Strict Transport Security\s+(.+)/', $line, $matches)) {
                $hstsValue = trim($matches[1]);
                $hstsEnabled = strpos($hstsValue, 'days') !== false;
                $results['security_headers']['HSTS'] = [
                    'present' => $hstsEnabled,
                    'value' => $hstsValue,
                    'description' => 'HTTP Strict Transport Security'
                ];
            }
            
            // 기타 보안 헤더들...
        }
    }

    private function parseVulnerabilities($lines, &$results)
    {
        $vulnerabilities = [
            'heartbleed' => 'Heartbleed',
            'ccs' => 'CCS',
            'ticketbleed' => 'Ticketbleed',
            'robot' => 'ROBOT',
            'crime' => 'CRIME',
            'breach' => 'BREACH',
            'poodle' => 'POODLE',
            'sweet32' => 'SWEET32',
            'freak' => 'FREAK',
            'drown' => 'DROWN',
            'logjam' => 'LOGJAM',
            'beast' => 'BEAST',
            'lucky13' => 'LUCKY13',
            'winshock' => 'Winshock',
            'rc4' => 'RC4'
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            
            foreach ($vulnerabilities as $key => $vulnName) {
                if (strpos($line, $vulnName) !== false) {
                    $vulnerable = strpos($line, 'VULNERABLE') !== false || 
                                 strpos($line, 'potentially NOT ok') !== false;
                    
                    $severity = 'ok';
                    if ($vulnerable) {
                        if (strpos($line, 'potentially') !== false) {
                            $severity = 'medium';
                        } else {
                            $severity = 'high';
                        }
                    }
                    
                    $results['vulnerabilities'][$key] = [
                        'vulnerable' => $vulnerable,
                        'severity' => $severity,
                        'description' => $line
                    ];
                    break;
                }
            }
        }
    }

    private function parseCipherSuites($lines, &$results)
    {
        $forwardSecrecy = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Forward Secrecy 체크
            if (preg_match('/FS is offered.*OK/', $line)) {
                $forwardSecrecy = true;
            }
            
            // 암호화 스위트 파싱
            if (preg_match('/FS is offered \(OK\)\s+(.+)/', $line, $matches)) {
                $cipherLine = $matches[1];
                $ciphers = preg_split('/\s+/', $cipherLine);
                
                foreach ($ciphers as $cipher) {
                    if (preg_match('/^[A-Z0-9-]+$/', $cipher) && strpos($cipher, 'ECDHE') !== false) {
                        $results['cipher_suites'][] = [
                            'name' => $cipher,
                            'key_exchange' => 'ECDHE',
                            'encryption' => $this->extractEncryption($cipher),
                            'mac' => $this->extractMAC($cipher),
                            'security_level' => 'high'
                        ];
                    }
                }
            }
        }
    }

    private function parseServerInfo($lines, &$results)
    {
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Server banner
            if (preg_match('/Server banner\s+(.+)/', $line, $matches)) {
                $results['server_banner'] = trim($matches[1]);
            }
            
            // Service detected
            if (preg_match('/Service detected:\s+(.+)/', $line, $matches)) {
                if (empty($results['server_banner'])) {
                    $results['server_banner'] = trim($matches[1]);
                }
            }
        }
    }

    private function getProtocolSecurityLevel($protocol)
    {
        if (strpos($protocol, 'TLS 1.3') !== false) return 'secure';
        if (strpos($protocol, 'TLS 1.2') !== false) return 'secure';
        if (strpos($protocol, 'TLS 1.1') !== false) return 'weak';
        if (strpos($protocol, 'TLS 1') !== false) return 'weak';
        if (strpos($protocol, 'SSL') !== false) return 'insecure';
        return 'unknown';
    }

    private function extractEncryption($cipher)
    {
        if (strpos($cipher, 'AES256') !== false) return 'AES256';
        if (strpos($cipher, 'AES128') !== false) return 'AES128';
        if (strpos($cipher, 'AES') !== false) return 'AES';
        if (strpos($cipher, 'CHACHA20') !== false) return 'CHACHA20';
        return 'Unknown';
    }

    private function extractMAC($cipher)
    {
        if (strpos($cipher, 'SHA384') !== false) return 'SHA384';
        if (strpos($cipher, 'SHA256') !== false) return 'SHA256';
        if (strpos($cipher, 'SHA') !== false) return 'SHA';
        if (strpos($cipher, 'GCM') !== false) return 'GCM';
        if (strpos($cipher, 'POLY1305') !== false) return 'POLY1305';
        return 'Unknown';
    }

    private function findTestSslPath()
    {
        $possiblePaths = [
            '/opt/testssl.sh/testssl.sh',
            '/usr/local/bin/testssl',
            '/usr/bin/testssl.sh',
            './testssl.sh'
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }
        
        throw new \Exception('testssl.sh executable not found. Please install testssl.sh first.');
    }
}