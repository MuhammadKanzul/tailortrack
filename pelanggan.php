<?php
session_start();
require_once 'config/database.php';
require_once 'models/Pelanggan.php';

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pelangganModel = new Pelanggan($pelangganCollection);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $success = false;
        $message = '';
        
        try {
            switch ($_POST['action']) {
                case 'create':
                    $data = [
                        'nama' => $_POST['nama'],
                        'telepon' => $_POST['telepon'],
                        'alamat' => $_POST['alamat'],
                        'ukuran_baju' => [
                            'panjang_baju' => (float)$_POST['panjang_baju'],
                            'lebar_baju' => (float)$_POST['lebar_baju'],
                            'panjang_lengan' => (float)$_POST['panjang_lengan'],
                            'lingkar_dada' => (float)$_POST['lingkar_dada']
                        ]
                    ];
                    if ($pelangganModel->createPelanggan($data)) {
                        $success = true;
                        $message = 'Data pelanggan berhasil ditambahkan';
                    }
                    break;

                case 'update':
                    $id = $_POST['id'];
                    $data = [
                        'nama' => $_POST['nama'],
                        'telepon' => $_POST['telepon'],
                        'alamat' => $_POST['alamat'],
                        'ukuran_baju' => [
                            'panjang_baju' => (float)$_POST['panjang_baju'],
                            'lebar_baju' => (float)$_POST['lebar_baju'],
                            'panjang_lengan' => (float)$_POST['panjang_lengan'],
                            'lingkar_dada' => (float)$_POST['lingkar_dada']
                        ]
                    ];
                    if ($pelangganModel->updatePelanggan($id, $data)) {
                        $success = true;
                        $message = 'Data pelanggan berhasil diperbarui';
                    }
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    if ($pelangganModel->deletePelanggan($id)) {
                        $success = true;
                        $message = 'Data pelanggan berhasil dihapus';
                    }
                    break;
            }
        } catch (Exception $e) {
            $message = 'Terjadi kesalahan: ' . $e->getMessage();
        }
        
        $_SESSION['flash'] = [
            'type' => $success ? 'success' : 'danger',
            'message' => $message
        ];
        
        header('Location: pelanggan.php');
        exit;
    }
}

include 'includes/header.php';

// Ambil data pelanggan dan konversi ke array
$pelanggan = iterator_to_array($pelangganModel->getAllPelanggan());
$totalPelanggan = count($pelanggan);
?>

<!-- CSS tambahan -->
<style>
.card {
    border: none;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 10px;
}
.card-header {
    background-color: #fff;
    border-bottom: 2px solid #f8f9fa;
    padding: 1.5rem;
}
.action-buttons .btn {
    padding: 0.5rem;
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    margin: 0 2px;
    transition: all 0.3s;
}
.action-buttons .btn:hover {
    transform: translateY(-2px);
}
.stats-card {
    background: linear-gradient(45deg, #2196F3, #1976D2);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.table th {
    font-weight: 600;
    color: #495057;
}
.ukuran-badge {
    display: inline-block;
    padding: 0.4em 0.8em;
    font-size: 0.75em;
    background: #e9ecef;
    border-radius: 6px;
    margin: 0.1em;
    color: #495057;
}
.modal-content {
    border: none;
    border-radius: 15px;
}
.modal-header {
    border-radius: 15px 15px 0 0;
    background: #f8f9fa;
}
.form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.6rem 1rem;
}
.form-control:focus {
    border-color: #2196F3;
    box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
}
.customer-info {
    display: flex;
    align-items: center;
}
.customer-avatar {
    width: 40px;
    height: 40px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    color: #495057;
    font-weight: bold;
}
</style>

<div class="container mt-4">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash']['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-card">
                <h4 class="mb-3">Total Pelanggan</h4>
                <h2 class="mb-0"><?= number_format($totalPelanggan) ?></h2>
                <small>Pelanggan terdaftar</small>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Data Pelanggan</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPelangganModal">
                <i class="fas fa-user-plus me-2"></i>Tambah Pelanggan
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Pelanggan</th>
                            <th>Kontak</th>
                            <th>Ukuran Baju</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pelanggan as $p): 
                            $p = (array)$p;
                            $ukuran = isset($p['ukuran_baju']) ? (array)$p['ukuran_baju'] : [
                                'panjang_baju' => 0,
                                'lebar_baju' => 0,
                                'panjang_lengan' => 0,
                                'lingkar_dada' => 0
                            ];
                            
                            // Get initials for avatar
                            $nama = $p['nama'] ?? '';
                            $initials = strtoupper(substr($nama, 0, 2));
                        ?>
                        <tr>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        <?= $initials ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($nama) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($p['alamat'] ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-phone-alt text-muted me-2"></i>
                                <?= htmlspecialchars($p['telepon'] ?? '') ?>
                            </td>
                            <td>
                                <span class="ukuran-badge">
                                    <i class="fas fa-ruler-vertical me-1"></i>P: <?= $ukuran['panjang_baju'] ?> cm
                                </span>
                                <span class="ukuran-badge">
                                    <i class="fas fa-ruler-horizontal me-1"></i>L: <?= $ukuran['lebar_baju'] ?> cm
                                </span>
                                <span class="ukuran-badge">
                                    <i class="fas fa-ruler me-1"></i>Lengan: <?= $ukuran['panjang_lengan'] ?> cm
                                </span>
                                <span class="ukuran-badge">
                                    <i class="fas fa-circle me-1"></i>Dada: <?= $ukuran['lingkar_dada'] ?> cm
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons text-center">
                                    <button class="btn btn-info edit-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editPelangganModal"
                                            data-id="<?= $p['_id'] ?>"
                                            data-nama="<?= htmlspecialchars($nama) ?>"
                                            data-telepon="<?= htmlspecialchars($p['telepon'] ?? '') ?>"
                                            data-alamat="<?= htmlspecialchars($p['alamat'] ?? '') ?>"
                                            data-panjang="<?= $ukuran['panjang_baju'] ?>"
                                            data-lebar="<?= $ukuran['lebar_baju'] ?>"
                                            data-lengan="<?= $ukuran['panjang_lengan'] ?>"
                                            data-dada="<?= $ukuran['lingkar_dada'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger delete-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deletePelangganModal"
                                            data-id="<?= $p['_id'] ?>"
                                            data-nama="<?= htmlspecialchars($nama) ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pelanggan -->
<div class="modal fade" id="addPelangganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                            <input type="text" class="form-control" name="telepon" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <textarea class="form-control" name="alamat" required rows="2"></textarea>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Ukuran Baju</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-ruler-vertical me-1"></i>
                                Panjang Baju (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="panjang_baju" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-ruler-horizontal me-1"></i>
                                Lebar Baju (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="lebar_baju" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-ruler me-1"></i>
                                Panjang Lengan (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="panjang_lengan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-circle me-1"></i>
                                Lingkar Dada (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="lingkar_dada" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pelanggan -->
<div class="modal fade" id="editPelangganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" id="edit_nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                            <input type="text" class="form-control" name="telepon" id="edit_telepon" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <textarea class="form-control" name="alamat" id="edit_alamat" required rows="2"></textarea>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Ukuran Baju</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-ruler-vertical me-1"></i>
                                Panjang Baju (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="panjang_baju" id="edit_panjang_baju" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-ruler-horizontal me-1"></i>
                                Lebar Baju (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="lebar_baju" id="edit_lebar_baju" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-ruler me-1"></i>
                                Panjang Lengan (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="panjang_lengan" id="edit_panjang_lengan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-circle me-1"></i>
                                Lingkar Dada (cm)
                            </label>
                            <input type="number" step="0.1" class="form-control" name="lingkar_dada" id="edit_lingkar_dada" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Pelanggan -->
<div class="modal fade" id="deletePelangganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-times me-2"></i>Hapus Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Apakah Anda yakin ingin menghapus pelanggan <strong><span id="delete_nama"></span></strong>?
                        <br>
                        <small>Tindakan ini tidak dapat dibatalkan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Edit button handler
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            document.querySelector('#edit_id').value = data.id;
            document.querySelector('#edit_nama').value = data.nama;
            document.querySelector('#edit_telepon').value = data.telepon;
            document.querySelector('#edit_alamat').value = data.alamat;
            document.querySelector('#edit_panjang_baju').value = data.panjang;
            document.querySelector('#edit_lebar_baju').value = data.lebar;
            document.querySelector('#edit_panjang_lengan').value = data.lengan;
            document.querySelector('#edit_lingkar_dada').value = data.dada;
        });
    });

    // Delete button handler
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            document.querySelector('#delete_id').value = data.id;
            document.querySelector('#delete_nama').textContent = data.nama;
        });
    });
});
</script>