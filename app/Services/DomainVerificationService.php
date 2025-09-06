<?php

namespace App\Services;

use App\Models\Domain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DomainVerificationService
{
    /**
     * TXT 레코드를 통한 도메인 인증
     */
    public static function verifyByTxtRecord(Domain $domain): bool
    {
        try {
            $domainName = $domain->domain_only;
            $expectedValue = "devteam-verification={$domain->verification_token}";
            
            Log::info('Starting TXT record verification', [
                'domain' => $domainName,
                'expected_value' => $expectedValue,
                'token' => $domain->verification_token
            ]);
            
            // DNS TXT 레코드 조회
            $txtRecords = @dns_get_record($domainName, DNS_TXT);
            
            if ($txtRecords === false) {
                Log::error('DNS query failed', [
                    'domain' => $domainName,
                    'error' => 'dns_get_record returned false'
                ]);
                return false;
            }
            
            if (empty($txtRecords)) {
                Log::info('No TXT records found', ['domain' => $domainName]);
                return false;
            }
            
            Log::info('TXT records found', [
                'domain' => $domainName,
                'records' => array_column($txtRecords, 'txt')
            ]);
            
            // 인증 TXT 레코드 찾기
            foreach ($txtRecords as $record) {
                if (isset($record['txt'])) {
                    // 따옴표 제거 후 비교
                    $recordValue = trim($record['txt'], '"\'');
                    Log::info('Comparing TXT record', [
                        'found' => $recordValue,
                        'expected' => $expectedValue,
                        'match' => $recordValue === $expectedValue
                    ]);
                    
                    if ($recordValue === $expectedValue) {
                        Log::info('Domain verification successful via TXT record');
                        $domain->markAsVerified('txt_record');
                        return true;
                    }
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Domain verification via TXT record failed', [
                'domain' => $domain->domain_only,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * 파일 업로드를 통한 도메인 인증
     */
    public static function verifyByFileUpload(Domain $domain): bool
    {
        try {
            $domainUrl = $domain->url;
            $fileName = $domain->verification_file_name;
            $expectedContent = $domain->verification_file_content;
            
            // 파일이 루트 디렉토리에 있는지 확인
            $verificationUrl = rtrim($domainUrl, '/') . '/' . $fileName;
            
            $response = Http::timeout(30)
                ->withOptions(['verify' => false]) // SSL 인증서 검증 비활성화 (필요시)
                ->get($verificationUrl);
            
            if (!$response->successful()) {
                return false;
            }
            
            $fileContent = trim($response->body());
            $expectedContentTrimmed = trim($expectedContent);
            
            if ($fileContent === $expectedContentTrimmed) {
                
                // markAsVerified가 관련 도메인도 자동 인증
                $domain->markAsVerified('file_upload');
                
                // 자동 인증된 도메인 수 로그
                self::logAutoVerifiedDomains($domain);
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 자동 인증 시도 (TXT 레코드 우선, 실패시 파일 업로드)
     */
    public static function attemptVerification(Domain $domain): string
    {
        // 이미 인증된 도메인은 건너뛰기
        if ($domain->is_verified) {
            return 'already_verified';
        }
        
        // 1. TXT 레코드 인증 시도
        if (self::verifyByTxtRecord($domain)) {
            return 'txt_record_success';
        }
        
        // 2. 파일 업로드 인증 시도
        if (self::verifyByFileUpload($domain)) {
            return 'file_upload_success';
        }
        
        return 'verification_failed';
    }
    
    /**
     * 같은 호스트네임의 관련 도메인들을 자동 인증
     */
    public static function verifyRelatedDomains(Domain $verifiedDomain): int
    {
        $hostname = parse_url($verifiedDomain->url, PHP_URL_HOST);
        if (!$hostname) {
            return 0;
        }
        
        // 같은 사용자의 같은 호스트네임을 가진 미인증 도메인들 찾기
        $unverifiedDomains = Domain::where('user_id', $verifiedDomain->user_id)
            ->where('id', '!=', $verifiedDomain->id)
            ->where('is_verified', false)
            ->get()
            ->filter(function ($domain) use ($hostname) {
                return parse_url($domain->url, PHP_URL_HOST) === $hostname;
            });
        
        $count = 0;
        // 찾은 도메인들 자동 인증
        foreach ($unverifiedDomains as $domain) {
            $domain->is_verified = true;
            $domain->verified_at = now();
            $domain->verification_method = 'auto_hostname';
            $domain->save();
            $count++;
        }
        
        return $count;
    }
    
    /**
     * 도메인 추가 시 같은 호스트네임으로 이미 인증된 도메인이 있는지 확인
     */
    public static function checkExistingVerification(Domain $newDomain): bool
    {
        $hostname = parse_url($newDomain->url, PHP_URL_HOST);
        
        if (!$hostname) {
            return false;
        }
        
        // 같은 사용자의 인증된 도메인들 조회
        $verifiedDomains = Domain::where('user_id', $newDomain->user_id)
            ->where('id', '!=', $newDomain->id)
            ->where('is_verified', true)
            ->get();
        
        // 같은 호스트네임 찾기
        $verifiedDomain = $verifiedDomains->first(function ($domain) use ($hostname) {
            $domainHostname = parse_url($domain->url, PHP_URL_HOST);
            return $domainHostname === $hostname;
        });
        
        if ($verifiedDomain) {
            // 직접 업데이트 쿼리 실행
            Domain::where('id', $newDomain->id)->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verification_method' => 'auto_hostname'
            ]);
            
            // 객체 새로고침
            $newDomain->refresh();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 자동 인증된 도메인 수를 로깅
     */
    private static function logAutoVerifiedDomains(Domain $domain): void
    {
        $hostname = parse_url($domain->url, PHP_URL_HOST);
        if (!$hostname) {
            return;
        }
        
        $autoVerifiedCount = Domain::where('user_id', $domain->user_id)
            ->where('id', '!=', $domain->id)
            ->where('verification_method', 'auto_hostname')
            ->where('verified_at', '>=', now()->subSeconds(5))
            ->get()
            ->filter(function ($d) use ($hostname) {
                return parse_url($d->url, PHP_URL_HOST) === $hostname;
            })
            ->count();
    }
}