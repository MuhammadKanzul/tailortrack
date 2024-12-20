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
$user = $userModel->getUserById($_SESSION['user_id']);

include 'includes/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash']['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Profil Saya</h4>
                </div>
                <div class="card-body p-4">
                    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-auto position-relative">
                                <div class="profile-avatar">
                                    <?php if (isset($user->foto_profil) && file_exists('uploads/profile/' . $user->foto_profil)): ?>
                                        <img src="uploads/profile/<?= htmlspecialchars($user->foto_profil) ?>" 
                                             alt="Profile" 
                                             class="rounded-circle"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                                    <?php endif; ?>
                                    <div class="change-photo-overlay">
                                        <label for="foto_profil" class="change-photo-btn">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" id="foto_profil" name="foto_profil" accept="image/*" class="d-none">
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="mb-1"><?= htmlspecialchars($user->username) ?></h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-shield-alt me-1"></i>Role: <?= ucfirst(htmlspecialchars($user->role)) ?>
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Bergabung: <?= $user->created_at->toDateTime()->format('d M Y') ?>
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" name="nama" 
                                           value="<?= htmlspecialchars($user->nama ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= htmlspecialchars($user->email ?? '') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" name="telepon" 
                                           value="<?= htmlspecialchars($user->telepon ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control" name="alamat" 
                                           value="<?= htmlspecialchars($user->alamat ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="change_password.php" class="btn btn-outline-primary">
                                <i class="fas fa-key me-2"></i>Ubah Password
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-avatar {
    width: 100px;
    height: 100px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
    margin-right: 1rem;
    overflow: hidden;
}

.change-photo-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    padding: 5px;
    display: flex;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-avatar:hover .change-photo-overlay {
    opacity: 1;
}

.change-photo-btn {
    color: white;
    cursor: pointer;
    padding: 5px;
}

.change-photo-btn:hover {
    color: #e9ecef;
}

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

<!-- Script untuk preview foto sebelum upload -->
<script>
document.getElementById('foto_profil').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const profileAvatar = document.querySelector('.profile-avatar');
            // Hapus ikon default jika ada
            profileAvatar.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                <div class="change-photo-overlay">
                    <label for="foto_profil" class="change-photo-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="foto_profil" name="foto_profil" accept="image/*" class="d-none">
                </div>
            `;
        }
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php include 'includes/footer.php'; ?> 