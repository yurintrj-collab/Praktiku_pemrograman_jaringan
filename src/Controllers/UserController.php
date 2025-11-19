<?php
namespace Src\Controllers;

use Src\Repositories\UserRepository;
use Src\Validation\Validator;
use Src\Middlewares\AuthMiddleware;

class UserController extends BaseController {
    public function index() {
        // Authentication required
    AuthMiddleware::user($this->cfg);
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 10)));
        $search = $_GET['search'] ?? null;
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'desc';
        
        $repo = new UserRepository($this->cfg);
        $result = $repo->paginate($page, $perPage, $search, $sort, $order);
        
        $this->ok($result);
    }
    
    public function show($id) {
        // Authentication required
      // AuthMiddleware::user($this->cfg);
        
        $repo = new UserRepository($this->cfg);
        $user = $repo->find((int)$id);
        
        $user ? $this->ok($user) : $this->error(404, 'User not found');
    }
    
    public function store() {
        // Only admin can create users
       // AuthMiddleware::admin($this->cfg);
        
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $validator = Validator::make($input, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|min:6|max:72',
            'role' => 'enum:user,admin'
        ]);
        
        if($validator->fails()) {
            return $this->error(422, 'Validation error', $validator->errors());
        }
        
        $hash = password_hash($input['password'], PASSWORD_DEFAULT);
        $repo = new UserRepository($this->cfg);
        
        try {
            $user = $repo->create(
                $input['name'],
                $input['email'],
                $hash,
                $input['role'] ?? 'user'
            );
            $this->ok($user, 201);
        } catch (\Throwable $e) {
            if(strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->error(409, 'Email already exists');
            } else {
                $this->error(400, 'Create failed', ['details' => $e->getMessage()]);
            }
        }
    }
    
    public function update($id) {
        // Authentication required
    //   $authUser = AuthMiddleware::user($this->cfg);
        
        // Users can update themselves, admins can update anyone
        // if($authUser['role'] !== 'admin' && $authUser['sub'] != $id) {
        //     $this->error(403, 'Forbidden');
        // }
        
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $rules = [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:150'
        ];
        
        // Only admin can change role
        // if($authUser['role'] === 'admin') {
        //     $rules['role'] = 'enum:user,admin';
        // }
        
        $validator = Validator::make($input, $rules);
        
        if($validator->fails()) {
            return $this->error(422, 'Validation error', $validator->errors());
        }
        
        $repo = new UserRepository($this->cfg);
        
        try {
            $updateData = [
                'name' => $input['name'],
                'email' => $input['email']
            ];
            
        //    if($authUser['role'] === 'admin' && isset($input['role'])) {
                $updateData['role'] = $input['role'];
          //  }
            
            $user = $repo->update((int)$id, $updateData);
            $user ? $this->ok($user) : $this->error(404, 'User not found');
        } catch (\Throwable $e) {
            if(strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->error(409, 'Email already exists');
            } else {
                $this->error(400, 'Update failed', ['details' => $e->getMessage()]);
            }
        }
    }
    
    public function destroy($id) {
        // Only admin can delete users
        AuthMiddleware::admin($this->cfg);
        
        $repo = new UserRepository($this->cfg);
        $success = $repo->delete((int)$id);
        
        $success ? $this->ok(['deleted' => true]) : $this->error(400, 'Delete failed');
    }
}