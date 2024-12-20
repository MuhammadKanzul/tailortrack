<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';
include 'includes/header.php';

// Cek autentikasi dan role admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userModel = new User($usersCollection);
$user = $userModel->getUserById($_SESSION['user_id']);
?>

<div class="container mt-4">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card profile-card">
                <div class="card-body text-center">
                    <div class="profile-image mb-3">
                        <?php if (isset($user->foto_profil) && file_exists('uploads/profile/' . $user->foto_profil)): ?>
                            <img src="uploads/profile/<?= htmlspecialchars($user->foto_profil) ?>" 
                                 alt="Profile" 
                                 class="rounded-circle img-thumbnail"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-6x text-primary"></i>
                        <?php endif; ?>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#uploadFotoModal">
                                <i class="fas fa-camera"></i> Ubah Foto
                            </button>
                        </div>
                    </div>
                    <h4 class="mb-2"><?= htmlspecialchars($user->nama) ?></h4>
                    <p class="text-muted mb-1"><?= htmlspecialchars($user->username) ?></p>
                    <p class="text-muted mb-3">
                        <span class="badge bg-<?= $user->role === 'admin' ? 'danger' : 'info' ?>">
                            <?= ucfirst($user->role) ?>
                        </span>
                    </p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Contact Information</h5>
                    <div class="mb-2">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <?= htmlspecialchars($user->email) ?>
                    </div>
                    <?php if (isset($user->telepon)): ?>
                    <div class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <?= htmlspecialchars($user->telepon) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($user->alamat)): ?>
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        <?= htmlspecialchars($user->alamat) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Activity & Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="about-tab" data-bs-toggle="tab" href="#about" role="tab">
                                <i class="fas fa-user me-2"></i>About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Security
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="profileTabsContent">
                        <!-- About Tab -->
                        <div class="tab-pane fade show active" id="about" role="tabpanel">
                            <h5>Profile Details</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Full Name:</strong><br><?= htmlspecialchars($user->nama) ?></p>
                                    <p class="mb-2"><strong>Username:</strong><br><?= htmlspecialchars($user->username) ?></p>
                                    <p class="mb-2"><strong>Email:</strong><br><?= htmlspecialchars($user->email) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Role:</strong><br><?= ucfirst($user->role) ?></p>
                                    <p class="mb-2"><strong>Member Since:</strong><br>
                                        <?= date('d M Y', $user->created_at->toDateTime()->getTimestamp()) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <h5>Change Password</h5>
                            <hr>
                            <form method="POST" action="update_password.php">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="update_profile.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($user->nama) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user->email) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="telepon" value="<?= isset($user->telepon) ? htmlspecialchars($user->telepon) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="alamat" rows="3"><?= isset($user->alamat) ? htmlspecialchars($user->alamat) : '' ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal upload foto -->
<div class="modal fade" id="uploadFotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Current Profile Photo -->
                    <div class="text-center mb-4">
                        <h6 class="mb-3">Foto Profil Saat Ini</h6>
                        <div class="current-photo mx-auto">
                            <?php if (isset($user->foto_profil) && file_exists('uploads/profile/' . $user->foto_profil)): ?>
                                <img src="uploads/profile/<?= htmlspecialchars($user->foto_profil) ?>" 
                                     alt="Current Profile" 
                                     class="rounded-circle img-thumbnail">
                                <button type="button" class="btn btn-danger btn-sm delete-photo-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deletePhotoModal">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-6x text-primary"></i>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Upload New Photo -->
                    <div class="upload-section">
                        <h6 class="mb-3">Upload Foto Baru</h6>
                        <div id="preview-container" class="d-none mb-3">
                            <div class="preview-wrapper mx-auto">
                                <img id="preview-image" src="#" alt="Preview" class="rounded-circle img-thumbnail">
                            </div>
                        </div>
                        <div class="custom-file-upload">
                            <input type="file" class="form-control" name="foto_profil" 
                                   id="foto_profil" accept="image/*" required>
                        </div>
                        <div class="upload-info mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Format yang didukung: JPG, JPEG, PNG
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Ukuran maksimal: 2MB
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Foto -->
<div class="modal fade" id="deletePhotoModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-0">Apakah Anda yakin ingin menghapus foto profil?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form action="update_profile.php" method="POST">
                    <input type="hidden" name="action" value="delete_photo">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan script untuk preview foto -->
<script>
document.getElementById('foto_profil').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.classList.remove('d-none');
        }
        
        reader.readAsDataURL(file);
    }
});
</script>

<?php include 'includes/footer.php'; ?>