<?php
namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class UrlSecurityValidator
{
   private static $blockedHosts = [
       'localhost',
       '127.0.0.1',
       '0.0.0.0',
       '10.',      // 10.0.0.0/8
       '172.16.',  // 172.16.0.0/12  
       '192.168.', // 192.168.0.0/16
       '169.254.', // Link-local
       'metadata.google.internal',
       'metadata',
   ];

   private static $blockedPorts = [
       22, 23, 25, 53, 110, 143, 993, 995, // Common services
       3306, 5432, 6379, 27017, // Databases
       8080, 8000, 9000, // Development ports
   ];

   public static function validate(string $url): array
   {
       $errors = [];
       
       // Basic URL format validation
       if (!filter_var($url, FILTER_VALIDATE_URL)) {
           $errors[] = '유효하지 않은 URL 형식입니다.';
           return $errors;
       }

       $parsed = parse_url($url);
       
       if (!$parsed) {
           $errors[] = 'URL을 파싱할 수 없습니다.';
           return $errors;
       }

       // Only allow HTTP/HTTPS
       if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
           $errors[] = 'HTTP 또는 HTTPS URL만 허용됩니다.';
       }

       $host = $parsed['host'] ?? '';
       
       // Check blocked hosts
       foreach (self::$blockedHosts as $blocked) {
           if (str_starts_with(strtolower($host), strtolower($blocked))) {
               $errors[] = '내부 네트워크 주소는 접근할 수 없습니다.';
               break;
           }
       }

       // Check IP address ranges
       if (filter_var($host, FILTER_VALIDATE_IP)) {
           if (self::isPrivateIP($host)) {
               $errors[] = '프라이빗 IP 주소는 접근할 수 없습니다.';
           }
       }

       // Check port
       $port = $parsed['port'] ?? (($parsed['scheme'] === 'https') ? 443 : 80);
       if (in_array($port, self::$blockedPorts) && !in_array($port, [80, 443])) {
           $errors[] = '허용되지 않은 포트입니다.';
       }

       // Check for suspicious paths
       $path = $parsed['path'] ?? '';
       if (str_contains($path, '..') || str_contains($path, '//')) {
           $errors[] = '잘못된 경로입니다.';
       }

       return $errors;
   }

   public static function validateWithDnsCheck(string $url): array
   {
       $errors = self::validate($url);
       
       if (!empty($errors)) {
           return $errors;
       }
       
       $parsed = parse_url($url);
       $host = $parsed['host'] ?? '';
       
       // DNS 해상도 검사
       if (!self::isValidDnsHost($host)) {
           $errors[] = '도메인을 해석할 수 없거나 접근이 제한된 주소입니다.';
       }
       
       return $errors;
   }

   private static function isValidDnsHost(string $host): bool
   {
       // IP 주소인 경우 이미 이전 검증에서 처리됨
       if (filter_var($host, FILTER_VALIDATE_IP)) {
           return true;
       }
       
       // DNS 조회
       $ips = @dns_get_record($host, DNS_A);
       if (empty($ips)) {
           return false;
       }
       
       // 해석된 IP들이 모두 안전한지 확인
       foreach ($ips as $record) {
           if (isset($record['ip']) && self::isPrivateIP($record['ip'])) {
               return false;
           }
       }
       
       return true;
   }

   private static function isPrivateIP(string $ip): bool
   {
       return !filter_var($ip, FILTER_VALIDATE_IP, 
           FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
       );
   }

   public static function testConnection(string $url): bool
   {
       try {
           $context = stream_context_create([
               'http' => [
                   'method' => 'HEAD',
                   'timeout' => 10,
                   'ignore_errors' => true,
                   'follow_location' => 0, // 리다이렉트 따라가지 않음
               ]
           ]);
           
           $headers = @get_headers($url, 1, $context);
           return $headers !== false;
       } catch (\Exception $e) {
           return false;
       }
   }
}