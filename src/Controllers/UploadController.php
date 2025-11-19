<?php
namespace Src\Controllers;

use Src\Middlewares\AuthMiddleware;

class UploadController extends BaseController {
    public function store() {
        // Authentication required
        AuthMiddleware::user($this->cfg);
        
        if(($_SERVER['CONTENT_TYPE'] ?? '') && 
           str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
            return $this->error(415, 'Use multipart/form-data for upload');
        }
        
        if(empty($_FILES['file'])) {
            return $this->error(422, 'file is required');
        }
        
        $file = $_FILES['file'];
        
        if($file['error'] !== UPLOAD_ERR_OK) {
            return $this->error(400, 'Upload error: ' . $file['error']);
        }
        
        // Max 2MB
        if($file['size'] > 2 * 1024 * 1024) {
            return $this->error(422, 'Max file size is 2MB');
        }
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        
        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'application/pdf' => 'pdf'
        ];
        
        if(!isset($allowed[$mime])) {
            return $this->error(422, 'Invalid file type. Allowed: PNG, JPG, PDF');
        }
        
        // Generate random filename
        $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $uploadDir = __DIR__ . '/../../../uploads/';
        $dest = $uploadDir . $name;
        
        // Ensure upload directory exists
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if(!move_uploaded_file($file['tmp_name'], $dest)) {
            return $this->error(500, 'Save failed');
        }
        
        $this->ok([
            'path' => "/uploads/$name",
            'filename' => $name,
            'size' => $file['size'],
            'mime' => $mime
        ], 201);
    }
}