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
            // Security validation
            $securityErrors = UrlSecurityValidator::validateWithDnsCheck($url);
            if (!empty($securityErrors)) {
                throw new \Exception('Security validation failed: ' . implode(', ', $securityErrors));
            }
            
            // Update test status to running
            $test->update(['status' => 'running']);
            
            // Execute SSLyze test
            $results = $this->performSslyzeTest($url);
            
            // Parse and save results
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
        
        // Extract host and port from URL
        $parts = parse_url(trim($url));
        if (!$parts || empty($parts['host'])) {
            throw new \InvalidArgumentException('Please enter a valid URL.');
        }
        $host = $parts['host'];
        $port = $parts['port'] ?? 443;
        $target = $host . ':' . $port;
        
        // Execute sslyze
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
            throw new \RuntimeException($stderr !== '' ? $stderr : ($stdout !== '' ? $stdout : 'sslyze execution failed'));
        }
        
        // Read results
        $json = null;
        if ($disk->exists($relativePath)) {
            $json = $disk->get($relativePath);
        } elseif (is_file($absolutePath)) {
            $json = file_get_contents($absolutePath);
        } else {
            throw new \RuntimeException("Result file not found: {$relativePath}");
        }
        
        if (empty($json)) {
            throw new \RuntimeException('Empty JSON file was generated.');
        }
        
        return [
            'json' => $json,
            'file_path' => $relativePath,
            'absolute_path' => $absolutePath
        ];
    }
    
    /**
     * Clean up values to be JSON encodable
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
            throw new \Exception('JSON parsing failed or empty results');
        }
        
        $result = $data['server_scan_results'][0];
        $scan = $result['scan_result'] ?? [];
        
        // Detailed analysis
        $analysis = $this->performDetailedAnalysis($scan);
        
        // Extract metrics (for WebTest metrics field)
        $metrics = $this->extractMetrics($analysis);
        
        // Calculate grade and score
        $gradeData = $this->calculateGradeAndScore($analysis);
        
        // Sanitize data for JSON encoding
        $sanitizedAnalysis = $this->sanitizeForJson($analysis);
        $sanitizedMetrics = $this->sanitizeForJson($metrics);
        $sanitizedIssues = $this->sanitizeForJson($gradeData['issues']);
        $sanitizedRecommendations = $this->sanitizeForJson($gradeData['recommendations']);
        
        // raw_json might be too large, so save only necessary parts
        $sanitizedRawJson = $this->sanitizeRawJson($data);
        
        // Update WebTest
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
        
        // Clean up old tests per user
        if ($test->user_id) {
            WebTest::cleanupOldTests($test->user_id);
        }
    }
    
    /**
     * Extract and sanitize only necessary parts from raw JSON data
     */
    private function sanitizeRawJson($data)
    {
        // Keep only basic information
        $sanitized = [
            'server_info' => $data['server_info'] ?? null,
            'scan_status' => $data['server_scan_results'][0]['scan_status'] ?? null,
            'connectivity_status' => $data['server_scan_results'][0]['server_location']['connectivity_status'] ?? null,
        ];
        
        // Major scan results summary
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
        
        // Check for obsolete versions
        $oldVersions = ['ssl_2_0_cipher_suites', 'ssl_3_0_cipher_suites', 'tls_1_0_cipher_suites', 'tls_1_1_cipher_suites'];
        foreach ($oldVersions as $version) {
            if (isset($scan[$version]['result']['is_tls_version_supported']) && 
                $scan[$version]['result']['is_tls_version_supported']) {
                $versionName = str_replace('_cipher_suites', '', strtoupper(str_replace('_', ' ', $version)));
                $issues[] = "Obsolete protocol {$versionName} supported";
            }
        }
        
        if (!($versions['tls_1_2'] ?? false) && !($versions['tls_1_3'] ?? false)) {
            $issues[] = 'Modern TLS versions (1.2/1.3) not supported';
        }
        
        if (!($versions['tls_1_3'] ?? false)) {
            $issues[] = 'TLS 1.3 not supported';
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
        
        // TLS 1.2 cipher suite analysis
        if (isset($scan['tls_1_2_cipher_suites']['result']['accepted_cipher_suites'])) {
            $ciphers = $scan['tls_1_2_cipher_suites']['result']['accepted_cipher_suites'];
            $analysis['tls_1_2'] = $this->analyzeTls12Ciphers($ciphers);
        }
        
        // TLS 1.3 cipher suite analysis
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
            
            // Check PFS
            if (strpos($name, 'ECDHE') !== false || strpos($name, 'DHE') !== false) {
                $pfs_count++;
            }
            
            // Check weak ciphers
            if (strpos($name, 'RC4') !== false || strpos($name, 'DES') !== false || 
                strpos($name, 'NULL') !== false || strpos($name, 'EXPORT') !== false || 
                strpos($name, 'anon') !== false || $keySize < 128) {
                $weak++;
                $weak_ciphers[] = $name;
                if (count($issues) < 3) { // Show maximum 3
                    $issues[] = "Weak cipher suite: {$name}";
                }
            } elseif (strpos($name, 'CBC') !== false && strpos($name, 'SHA1') !== false) {
                // CBC with SHA1 is warning level
                $issues[] = "Obsolete cipher suite: {$name}";
            } else {
                $strong++;
            }
        }
        
        $total = count($ciphers);
        $pfs_ratio = $total > 0 ? round(($pfs_count / $total) * 100, 1) : 0;
        
        if ($weak > 0) {
            $issues[] = "Total {$weak} weak cipher suites detected";
        }
        
        if ($pfs_ratio < 100) {
            $issues[] = "PFS support rate {$pfs_ratio}% (100% recommended)";
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
            $analysis['issues'][] = 'Certificate information not found';
            return $analysis;
        }
        
        $deployment = $scan['certificate_info']['result']['certificate_deployments'][0];
        $cert = $deployment['received_certificate_chain'][0] ?? null;
        
        if (!$cert) {
            $analysis['issues'][] = 'No certificate chain information';
            return $analysis;
        }
        
        // Check expiry date
        $notAfter = $cert['not_valid_after'] ?? '';
        if ($notAfter) {
            $expiryDate = new \DateTime($notAfter);
            $now = new \DateTime();
            $diff = $now->diff($expiryDate);
            $daysToExpiry = $diff->invert ? -$diff->days : $diff->days;
            
            $analysis['details']['days_to_expiry'] = $daysToExpiry;
            
            if ($daysToExpiry <= 0) {
                $analysis['issues'][] = "Certificate expired";
            } elseif ($daysToExpiry <= 14) {
                $analysis['issues'][] = "Certificate expiry imminent ({$daysToExpiry} days left)";
            } elseif ($daysToExpiry <= 30) {
                $analysis['issues'][] = "Certificate renewal recommended ({$daysToExpiry} days left)";
            }
        }
        
        // Public key analysis
        $keyAlgo = $cert['public_key']['algorithm'] ?? '';
        $keySize = $cert['public_key']['key_size'] ?? 0;
        
        $analysis['details']['key_algorithm'] = $keyAlgo;
        $analysis['details']['key_size'] = $keySize;
        
        if ($keyAlgo === 'RSAPublicKey') {
            if ($keySize < 2048) {
                $analysis['issues'][] = "Insufficient RSA key size ({$keySize} bits, minimum 2048 bits required)";
            } elseif ($keySize < 3072) {
                $analysis['issues'][] = "RSA key size below recommendation ({$keySize} bits, 3072 bits recommended)";
            }
        }
        
        // Signature algorithm
        $sigAlgo = $cert['signature_hash_algorithm']['name'] ?? '';
        $analysis['details']['signature_algorithm'] = $sigAlgo;
        
        if (strpos(strtolower($sigAlgo), 'sha1') !== false || strpos(strtolower($sigAlgo), 'md5') !== false) {
            $analysis['issues'][] = "Weak signature algorithm used: {$sigAlgo}";
        }
        
        // Certificate chain validation
        $chainLength = count($deployment['received_certificate_chain'] ?? []);
        $analysis['details']['chain_length'] = $chainLength;
        
        if ($chainLength < 2) {
            $analysis['issues'][] = 'Incomplete certificate chain (possible missing intermediate certificate)';
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
                // OCSP Stapling normal
            } elseif ($status !== 'SUCCESSFUL') {
                $analysis['issues'][] = 'OCSP Stapling disabled or failed';
            } else {
                $analysis['issues'][] = "Certificate status abnormal: {$certStatus}";
            }
        } else {
            $analysis['issues'][] = 'OCSP Stapling not configured';
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
            if ($maxAge < 15552000) { // 6 months
                $analysis['issues'][] = 'HSTS max-age value is low (minimum 6 months recommended)';
            }
            
            if (!($hsts['include_subdomains'] ?? false)) {
                $analysis['issues'][] = 'HSTS includeSubDomains not set';
            }
            
            if (!($hsts['preload'] ?? false)) {
                $analysis['issues'][] = 'HSTS preload not set';
            }
        } else {
            $analysis['issues'][] = 'HSTS header not set';
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
                $analysis['issues'][] = 'Modern elliptic curves not supported';
            }
            
            // Check weak curves
            $weak_curves = ['secp192r1', 'secp224r1'];
            $has_weak = array_intersect($weak_curves, $analysis['supported']);
            if (!empty($has_weak)) {
                $analysis['issues'][] = 'Weak elliptic curves supported: ' . implode(', ', $has_weak);
            }
        }
        
        return $analysis;
    }
    
    private function extractMetrics(array $analysis): array
    {
        $metrics = [];
        
        // TLS version metrics
        $metrics['tls_1_2_supported'] = $analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false;
        $metrics['tls_1_3_supported'] = $analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false;
        
        // Cipher suite metrics
        if (isset($analysis['cipher_suites']['tls_1_2'])) {
            $metrics['cipher_total'] = $analysis['cipher_suites']['tls_1_2']['total'];
            $metrics['cipher_weak'] = $analysis['cipher_suites']['tls_1_2']['weak'];
            $metrics['pfs_ratio'] = $analysis['cipher_suites']['tls_1_2']['pfs_ratio'];
        }
        
        // Certificate metrics
        if (isset($analysis['certificate']['details'])) {
            $metrics['cert_days_to_expiry'] = $analysis['certificate']['details']['days_to_expiry'] ?? null;
            $metrics['cert_key_size'] = $analysis['certificate']['details']['key_size'] ?? null;
            $metrics['cert_key_algo'] = $analysis['certificate']['details']['key_algorithm'] ?? null;
        }
        
        // OCSP metrics
        $metrics['ocsp_stapling'] = ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL';
        
        // HSTS metrics
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
        
        // 1. TLS version evaluation (25 points)
        $tlsScore = 0;
        $tls12 = $analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false;
        $tls13 = $analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false;
        
        if ($tls13 && $tls12) {
            $tlsScore = 25; // Both TLS 1.3 and 1.2 supported
        } elseif ($tls12) {
            $tlsScore = 15; // Only TLS 1.2 supported
        }
        
        // Deduct points for obsolete protocols
        $oldProtocolCount = count($analysis['tls_versions']['issues']);
        $tlsScore -= min($oldProtocolCount * 5, 15);
        
        $score += max(0, $tlsScore);
        
        // 2. Cipher suite evaluation (25 points)
        $cipherScore = 0;
        if (isset($analysis['cipher_suites']['tls_1_2'])) {
            $tls12Ciphers = $analysis['cipher_suites']['tls_1_2'];
            
            // PFS ratio (10 points)
            $pfsRatio = $tls12Ciphers['pfs_ratio'] ?? 0;
            $cipherScore += ($pfsRatio / 100) * 10;
            
            // Deduct points for weak ciphers
            $weakCount = $tls12Ciphers['weak'] ?? 0;
            if ($weakCount === 0) {
                $cipherScore += 10; // No weak ciphers
            } else {
                $cipherScore -= min($weakCount * 3, 15);
            }
            
            // TLS 1.3 cipher suite bonus
            if (($analysis['cipher_suites']['tls_1_3']['total'] ?? 0) > 0) {
                $cipherScore += 5;
            }
        }
        
        $score += max(0, $cipherScore);
        
        // 3. Certificate evaluation (20 points)
        $certScore = 20;
        $certIssues = $analysis['certificate']['issues'] ?? [];
        
        foreach ($certIssues as $issue) {
            if (strpos($issue, 'expir') !== false) {
                if (strpos($issue, 'expired') !== false) {
                    $certScore = 0; // Expired certificate
                } elseif (strpos($issue, 'imminent') !== false) {
                    $certScore -= 10;
                } else {
                    $certScore -= 5;
                }
            } elseif (strpos($issue, 'key size') !== false) {
                $certScore -= 5;
            } elseif (strpos($issue, 'Weak signature') !== false) {
                $certScore -= 8;
            } elseif (strpos($issue, 'chain') !== false) {
                $certScore -= 5;
            }
        }
        
        // Key algorithm bonus
        $keyAlgo = $analysis['certificate']['details']['key_algorithm'] ?? '';
        $keySize = $analysis['certificate']['details']['key_size'] ?? 0;
        
        if ($keyAlgo === 'ECPublicKey' && $keySize >= 256) {
            $certScore += 3; // ECDSA bonus
        } elseif ($keyAlgo === 'RSAPublicKey' && $keySize >= 3072) {
            $certScore += 2; // RSA 3072+ bonus
        }
        
        $score += max(0, min(20, $certScore));
        
        // 4. OCSP Stapling evaluation (10 points)
        if (($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' && 
            ($analysis['ocsp']['certificate_status'] ?? '') === 'GOOD') {
            $score += 10;
        } elseif (empty($analysis['ocsp']['issues'])) {
            $score += 5; // Partial support
        }
        
        // 5. HTTP security headers evaluation (10 points)
        if (isset($analysis['http_headers']['hsts'])) {
            $hstsScore = 5; // Basic HSTS
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
        
        // 6. Elliptic curves evaluation (10 points)
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
            
            // Deduct points for weak curves
            if (!empty($analysis['elliptic_curves']['issues'])) {
                $curveScore -= 3;
            }
            
            $score += max(0, $curveScore);
        }
        
        // Collect issues
        foreach ($analysis as $category => $data) {
            if (isset($data['issues'])) {
                $issues = array_merge($issues, $data['issues']);
            }
        }
        
        // Generate recommendations
        $recommendations = $this->generateRecommendations($analysis);
        
        // Calculate grade
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
        // Adjusted to match attached grade criteria
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
        
        // TLS recommendations
        if (!($analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false)) {
            $recommendations[] = 'Enable TLS 1.3 to comply with the latest security standards.';
        }
        
        if (!empty($analysis['tls_versions']['issues'])) {
            foreach ($analysis['tls_versions']['issues'] as $issue) {
                if (strpos($issue, 'Obsolete protocol') !== false) {
                    $recommendations[] = 'Disable obsolete protocols (SSL 3.0, TLS 1.0/1.1).';
                    break;
                }
            }
        }
        
        // Cipher suite recommendations
        if (isset($analysis['cipher_suites']['tls_1_2'])) {
            $pfsRatio = $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0;
            if ($pfsRatio < 100) {
                $recommendations[] = 'Configure all cipher suites to support Perfect Forward Secrecy (PFS).';
            }
            
            if (($analysis['cipher_suites']['tls_1_2']['weak'] ?? 0) > 0) {
                $recommendations[] = 'Disable weak cipher suites (RC4, DES, NULL, EXPORT).';
            }
        }
        
        // Certificate recommendations
        if (!empty($analysis['certificate']['details'])) {
            $keyAlgo = $analysis['certificate']['details']['key_algorithm'] ?? '';
            $keySize = $analysis['certificate']['details']['key_size'] ?? 0;
            
            if ($keyAlgo === 'RSAPublicKey' && $keySize < 3072) {
                $recommendations[] = 'Upgrade RSA certificate key size to 3072 bits or higher, or consider switching to ECDSA certificate.';
            }
            
            $daysToExpiry = $analysis['certificate']['details']['days_to_expiry'] ?? 0;
            if ($daysToExpiry <= 30 && $daysToExpiry > 0) {
                $recommendations[] = 'Certificate expiry is approaching. Prepare for renewal.';
            }
        }
        
        // OCSP recommendations
        if (($analysis['ocsp']['status'] ?? '') !== 'SUCCESSFUL') {
            $recommendations[] = 'Enable OCSP Stapling to improve certificate status checking performance.';
        }
        
        // HSTS recommendations
        if (!isset($analysis['http_headers']['hsts'])) {
            $recommendations[] = 'Configure HSTS (HTTP Strict Transport Security) header.';
        } else {
            $hsts = $analysis['http_headers']['hsts'];
            if (($hsts['max_age'] ?? 0) < 31536000) {
                $recommendations[] = 'Set HSTS max-age to at least 1 year (31536000 seconds).';
            }
            if (!($hsts['include_subdomains'] ?? false)) {
                $recommendations[] = 'Add includeSubDomains directive to HSTS.';
            }
            if (!($hsts['preload'] ?? false)) {
                $recommendations[] = 'Consider registering for HSTS Preload list.';
            }
        }
        
        // Elliptic curves recommendations
        if (!empty($analysis['elliptic_curves']['issues'])) {
            $recommendations[] = 'Support modern elliptic curves (X25519, secp256r1, secp384r1).';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Your current SSL/TLS configuration is excellent. Continue regular monitoring.';
        }
        
        return array_unique($recommendations);
    }
}