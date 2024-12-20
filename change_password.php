<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userModel = new User($usersCollection);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Password baru dan konfirmasi password tidak cocok';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password baru minimal 6 karakter';
    } else {
        // Verifikasi password lama
        $user = $userModel->getUserById($_SESSION['user_id']);
        if (!password_verify($current_password, $user->password)) {
            $error = 'Password saat ini tidak valid';
        } else {
            // Update password
            try {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $result = $userModel->updateProfile($_SESSION['user_id'], ['password' => $hashed_password]);
                
                if ($result->getModifiedCount() > 0) {
                    $success = 'Password berhasil diubah';
                } else {
                    $error = 'Tidak ada perubahan password';
                }
            } catch (Exception $e) {
                $error = 'Terjadi kesalahan saat mengubah password';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-key me-2"></i>Ubah Password</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check"></i></span>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="profile.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.card-header {
    border-bottom: 2px solid #f8f9fa;
    padding: 1.5rem;
    border-radius: 15px 15px 0 0 !important;
}

.form-control, .input-group-text {
    border-radius: 8px;
}

.input-group > .input-group-text {
    border-right: none;
}

.input-group > .form-control {
    border-left: none;
}

.input-group-text {
    background: white;
    width: 45px;
    justify-content: center;
}

.form-control:focus {
    border-color: #dee2e6;
    box-shadow: none;
}

.input-group:focus-within {
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.input-group:focus-within .form-control,
.input-group:focus-within .input-group-text {
    border-color: #6366f1;
}
</style>

<?php include 'includes/footer.php'; ?> 