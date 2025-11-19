<?php
// === AUTOLOADER ===
spl_autoload_register(function($class) {
    $basePath = __DIR__ . '/../';
    $class = str_replace('\\', '/', $class);
    $paths = [
        "$basePath/src/$class.php",
        "$basePath/$class.php"
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require $file;
            break;
        }
    }
});

$cfg = require __DIR__ . '/../config/env.php';

use Src\Helpers\Response;
use Src\Middlewares\CorsMiddleware;

// === HANDLE CORS ===
CorsMiddleware::handle($cfg);
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// === DETEKSI PATH ===
$uri = strtok($_SERVER['REQUEST_URI'], '?');

// Ambil path tanpa "public" agar router tetap bisa cocok
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$baseDir = dirname($scriptName);
$path = str_replace($baseDir, '', $uri);
$path = '/' . trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Hilangkan /public dari path (fix utama)
$path = str_replace('/public', '', $path);

// === Debug log (bisa hapus kalau sudah fix)
error_log("URI: $uri");
error_log("SCRIPT_NAME: $scriptName");
error_log("BASE_DIR: $baseDir");
error_log("FINAL PATH: $path");

// === DEFINISI ROUTES ===
$routes = [
    ['GET', '/api/v1/health', 'Src\Controllers\HealthController@show'],
    ['GET', '/api/v1/version', 'Src\Controllers\HealthController@version'],
    ['GET', '/api/v1/contract', 'Src\Controllers\HealthController@contract'],
    ['POST', '/api/v1/auth/login', 'Src\Controllers\AuthController@login'],
    ['GET', '/api/v1/users', 'Src\Controllers\UserController@index'],
    ['GET', '/api/v1/users/{id}', 'Src\Controllers\UserController@show'],
    ['POST', '/api/v1/users', 'Src\Controllers\UserController@store'],
    ['PUT', '/api/v1/users/{id}', 'Src\Controllers\UserController@update'],
    ['DELETE', '/api/v1/users/{id}', 'Src\Controllers\UserController@destroy'],
    ['POST', '/api/v1/upload', 'Src\Controllers\UploadController@store'],
];

// === MATCH ROUTE ===
function matchRoute($routes, $method, $path) {
    foreach ($routes as $route) {
        [$m, $p, $h] = $route;
        if ($m !== $method) continue;

        $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $p);
        if (preg_match('#^' . $regex . '$#', $path, $matches)) {
            array_shift($matches);
            return [$h, $matches];
        }
    }
    return [null, null];
}

[$handler, $params] = matchRoute($routes, $method, $path);

if (!$handler) {
    Response::jsonError(404, "Route not found (path: $path)");
}

[$class, $action] = explode('@', $handler);
if (!class_exists($class) || !method_exists($class, $action)) {
    Response::jsonError(405, "Method not allowed: $handler");
}

call_user_func_array([new $class($cfg), $action], $params);