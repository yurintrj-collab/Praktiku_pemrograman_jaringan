<?php
namespace Src\Controllers;

use Src\Config\Database;
use Src\Helpers\Jwt;
use Src\Validation\Validator;

class AuthController extends BaseController {
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if($validator->fails()) {
            return $this->error(422, 'Validation error', $validator->errors());
        }
        
        $db = Database::conn($this->cfg);
        $stmt = $db->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ?');
        $stmt->execute([$input['email']]);
        $user = $stmt->fetch();
        
        if(!$user || !password_verify($input['password'], $user['password_hash'])) {
            return $this->error(401, 'Invalid credentials');
        }
        
        $payload = [
            'sub' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + 3600 // 1 hour
        ];
        
        $token = Jwt::sign($payload, $this->cfg['app']['jwt_secret']);
        
        $this->ok([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }
}