<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    if (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        try {
            // Cek apakah username sudah ada
            $existingUser = $usersCollection->findOne(['username' => $username]);
            
            if ($existingUser) {
                $error = 'Username sudah digunakan!';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user baru
                $result = $usersCollection->insertOne([
                    'username' => $username,
                    'password' => $hashed_password,
                    'role' => 'user',
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ]);
                
                if ($result->getInsertedId()) {
                    $success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $error = 'Terjadi kesalahan! Silakan coba lagi.';
                }
            }
        } catch (Exception $e) {
            error_log("Error in register: " . $e->getMessage());
            $error = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h4 class="card-title mb-1">Register</h4>
                        <p class="text-muted">Buat akun baru</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username" required 
                                       minlength="3" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            </div>
                            <small class="text-muted">Minimal 3 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            </div>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </button>
                        
                        <p class="text-center mb-0">
                            Sudah punya akun? 
                            <a href="login.php" class="text-primary">Login di sini</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.input-group-text {
    background: white;
    border-right: none;
}

.form-control {
    border-left: none;
}

.input-group:focus-within {
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.input-group:focus-within .form-control,
.input-group:focus-within .input-group-text {
    border-color: #6366f1;
}

.form-control:focus {
    border-color: #dee2e6;
    box-shadow: none;
}
</style>

<?php include 'includes/footer.php'; ?> 