<?php
namespace Src\Controllers;

class HealthController extends BaseController {

    public function show() {
        $this->ok([
            'status' => 'ok',
            'time' => date('c')
        ]);
    }

    public function contract() {
        // Path absolut ke file contract
        $file = __DIR__ . '/../../public/api_contract.php';

        if (file_exists($file)) {
            // Pastikan tipe konten adalah HTML agar browser menampilkan tabel
            header('Content-Type: text/html; charset=utf-8');
            include $file;
            exit;
        } else {
            // Jika file tidak ditemukan, tampilkan pesan error
            $this->error('File tidak ditemukan: ' . $file, 404);
        }
    }
}