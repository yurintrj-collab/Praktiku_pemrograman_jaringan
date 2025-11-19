<?php
namespace Src\Helpers;

class Ratelimiter {
    public static function check($key, $max = 60, $window = 60) {
        $logsDir = __DIR__ . '/../../logs/';
        if(!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
        
        $file = $logsDir . 'ratelimit_' . md5($key) . '.txt';
        $now = time();
        
        $timestamps = [];
        if(file_exists($file)) {
            $content = trim(file_get_contents($file));
            if(!empty($content)) {
                $timestamps = array_filter(
                    array_map('intval', explode("\n", $content)),
                    function($timestamp) use ($window, $now) {
                        return $timestamp > $now - $window;
                    }
                );
            }
        }
        
        if(count($timestamps) >= $max) {
            return false;
        }
        
        $timestamps[] = $now;
        file_put_contents($file, implode("\n", $timestamps));
        
        return true;
    }
}