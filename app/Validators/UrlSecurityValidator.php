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
       3306, 5432, 6379, 27017,            // Databases
       8080, 8000, 9000,                   // Development ports
   ];

   public static function validate(string $url): array
   {
       $errors = [];
       
       // Basic URL format validation
       if (!filter_var($url, FILTER_VALIDATE_URL)) {
           $errors[] = 'Invalid URL format.';
           return $errors;
       }

       $parsed = parse_url($url);
       
       if (!$parsed) {
           $errors[] = 'Unable to parse the URL.';
           return $errors;
       }

       // Only allow HTTP/HTTPS
       if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
           $errors[] = 'Only HTTP or HTTPS URLs are allowed.';
       }

       $host = $parsed['host'] ?? '';
       
       // Check blocked hosts
       foreach (self::$blockedHosts as $blocked) {
           if (str_starts_with(strtolower($host), strtolower($blocked))) {
               $errors[] = 'Access to internal network addresses is not allowed.';
               break;
           }
       }

       // Check IP address ranges
       if (filter_var($host, FILTER_VALIDATE_IP)) {
           if (self::isPrivateIP($host)) {
               $errors[] = 'Private IP addresses are not allowed.';
           }
       }

       // Check port
       $port = $parsed['port'] ?? (($parsed['scheme'] === 'https') ? 443 : 80);
       if (in_array($port, self::$blockedPorts) && !in_array($port, [80, 443])) {
           $errors[] = 'The port is not allowed.';
       }

       // Check for suspicious paths
       $path = $parsed['path'] ?? '';
       if (str_contains($path, '..') || str_contains($path, '//')) {
           $errors[] = 'Invalid path.';
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
       
       // DNS resolution check
       if (!self::isValidDnsHost($host)) {
           $errors[] = 'The domain cannot be resolved or the address is restricted.';
       }
       
       return $errors;
   }

   private static function isValidDnsHost(string $host): bool
   {
       // If it's an IP address, it was already handled earlier
       if (filter_var($host, FILTER_VALIDATE_IP)) {
           return true;
       }
       
       // DNS lookup
       $ips = @dns_get_record($host, DNS_A);
       if (empty($ips)) {
           return false;
       }
       
       // Ensure all resolved IPs are safe
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
                   'follow_location' => 0, // do not follow redirects
               ]
           ]);
           
           $headers = @get_headers($url, 1, $context);
           return $headers !== false;
       } catch (\Exception $e) {
           return false;
       }
   }
}