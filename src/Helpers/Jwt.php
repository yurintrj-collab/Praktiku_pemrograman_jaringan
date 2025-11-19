<?php
namespace Src\Helpers;

class Jwt {
    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    public static function base64url_decode($data) {
        $padding = strlen($data) % 4;
        if ($padding) {
            $data .= str_repeat('=', 4 - $padding);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    public static function sign(array $payload, string $secret, string $alg = 'HS256') {
        $header = ['typ' => 'JWT', 'alg' => $alg];
        
        $segments = [
            self::base64url_encode(json_encode($header)),
            self::base64url_encode(json_encode($payload))
        ];
        
        $signing_input = implode('.', $segments);
        $signature = hash_hmac('sha256', $signing_input, $secret, true);
        $segments[] = self::base64url_encode($signature);
        
        return implode('.', $segments);
    }
    
    public static function verify(string $jwt, string $secret) {
        $parts = explode('.', $jwt);
        if(count($parts) !== 3) {
            return null;
        }
        
        [$headerB64, $payloadB64, $signatureB64] = $parts;
        
        // Verify signature
        $signing_input = $headerB64 . '.' . $payloadB64;
        $signature = self::base64url_decode($signatureB64);
        $expected_signature = hash_hmac('sha256', $signing_input, $secret, true);
        
        if(!hash_equals($expected_signature, $signature)) {
            return null;
        }
        
        $payload = json_decode(self::base64url_decode($payloadB64), true);
        
        // Check expiration
        if(isset($payload['exp']) && time() > $payload['exp']) {
            return null;
        }
        
        return $payload;
    }
}