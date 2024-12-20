<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userModel = new User($usersCollection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete photo action
    if (isset($_POST['action']) && $_POST['action'] === 'delete_photo') {
        $user = $userModel->getUserById($_SESSION['user_id']);
        $upload_path = 'uploads/profile/';
        
        if (isset($user->foto_profil) && file_exists($upload_path . $user->foto_profil)) {
            unlink($upload_path . $user->foto_profil);
            $userModel->updateProfile($_SESSION['user_id'], ['foto_profil' => null]);
            $_SESSION['success'] = 'Foto profil berhasil dihapus';
        }
        
        header('Location: users.php');
        exit;
    }

    $data = [
        'nama' => $_POST['nama'] ?? null,
        'email' => $_POST['email'] ?? null,
        'telepon' => $_POST['telepon'] ?? null,
        'alamat' => $_POST['alamat'] ?? null
    ];
    
    // Filter out null values
    $data = array_filter($data, function($value) {
        return $value !== null;
    });
    
    // Handle foto upload
    $foto = null;
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto_profil']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            if ($_FILES['foto_profil']['size'] <= 2 * 1024 * 1024) { // 2MB max
                $foto = $_FILES['foto_profil'];
            } else {
                $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 2MB.';
            }
        } else {
            $_SESSION['error'] = 'Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.';
        }
    }
    
    try {
        $userModel->updateProfile($_SESSION['user_id'], $data, $foto);
        $_SESSION['success'] = 'Profil berhasil diperbarui';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Terjadi kesalahan saat memperbarui profil';
    }
    
    header('Location: users.php');
    exit;
} 