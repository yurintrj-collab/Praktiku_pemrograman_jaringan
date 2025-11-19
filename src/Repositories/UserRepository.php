<?php
namespace Src\Repositories;

use PDO;
use Src\Config\Database;

class UserRepository {
    private PDO $db;
    
    public function __construct(array $cfg) {
        $this->db = Database::conn($cfg);
    }
    
    public function paginate($page, $perPage, $search = null, $sort = 'id', $order = 'desc') {
        $offset = ($page - 1) * $perPage;
        
        // Whitelist sort columns for security
        $allowedSort = ['id', 'name', 'email', 'created_at', 'updated_at'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'id';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        $where = '';
        $params = [];
        
        if($search) {
            $where = 'WHERE name LIKE ? OR email LIKE ?';
            $params = ["%$search%", "%$search%"];
        }
        
        // Get total count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users $where");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        
        // Get data
        $sql = "SELECT id, name, email, role, created_at, updated_at 
                FROM users $where 
                ORDER BY $sort $order 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        if($search) {
            $stmt->execute(array_merge($params, [
                ':limit' => $perPage,
                ':offset' => $offset
            ]));
        } else {
            $stmt->execute([
                ':limit' => $perPage,
                ':offset' => $offset
            ]);
        }
        
        $lastPage = max(1, ceil($total / $perPage));
        
        return [
            'data' => $stmt->fetchAll(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ];
    }
    
    public function find($id) {
        $stmt = $this->db->prepare('
            SELECT id, name, email, role, created_at, updated_at 
            FROM users 
            WHERE id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($name, $email, $hash, $role = 'user') {
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare('
                INSERT INTO users(name, email, password_hash, role) 
                VALUES(?, ?, ?, ?)
            ');
            $stmt->execute([$name, $email, $hash, $role]);
            
            $id = (int)$this->db->lastInsertId();
            $this->db->commit();
            
            return $this->find($id);
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        foreach($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount() > 0 ? $this->find($id) : null;
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}