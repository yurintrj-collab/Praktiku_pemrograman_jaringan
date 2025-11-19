<?php
namespace Src\Middlewares;

use Src\Helpers\Response;
use Src\Helpers\Jwt;

class AuthMiddleware {
    public static function user(array $cfg) {
        // Debug: log semua headers
        error_log("All headers: " . print_r(getallheaders(), true));
        
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';
        error_log("Auth header raw: " . $header);
        
        if (empty($header)) {
            error_log("No authorization header found");
            Response::jsonError(401, 'Missing token');
        }
        
        if (!preg_match('/Bearer\s+(\S+)/', $header, $matches)) {
            error_log("Bearer pattern not matched");
            Response::jsonError(401, 'Invalid token format');
        }
        
        $token = $matches[1];
        error_log("Token extracted: " . substr($token, 0, 50) . "...");
        
        $payload = Jwt::verify($token, $cfg['app']['jwt_secret']);
        if (!$payload) {
            error_log("Token verification failed");
            Response::jsonError(401, 'Invalid or expired token');
        }
        
        error_log("Token verified successfully for user: " . $payload['email']);
        return $payload;
    }
    
    public static function admin(array $cfg) {
        $payload = self::user($cfg);
        
        if (($payload['role'] ?? 'user') !== 'admin') {
            Response::jsonError(403, 'Forbidden - Admin access required');
        }
        
        return $payload;
    }
}