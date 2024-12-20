<?php
session_start();
require_once 'config/database.php';
require_once 'models/StokBahan.php';

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';

$stokBahanModel = new StokBahan($stokCollection);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $success = false;
        $message = '';
        
        try {
            switch ($_POST['action']) {
                case 'create':
                    $data = [
                        'nama_bahan' => $_POST['nama_bahan'],
                        'jenis_bahan' => $_POST['jenis_bahan'],
                        'jumlah' => (float)$_POST['jumlah'],
                        'satuan' => $_POST['satuan'],
                        'harga_per_satuan' => (float)$_POST['harga_per_satuan'],
                        'keterangan' => $_POST['keterangan']
                    ];
                    if($stokBahanModel->createStokBahan($data)) {
                        $success = true;
                        $message = 'Bahan berhasil ditambahkan!';
                    }
                    break;

                case 'update':
                    $id = $_POST['id'];
                    $data = [
                        'nama_bahan' => $_POST['nama_bahan'],
                        'jenis_bahan' => $_POST['jenis_bahan'],
                        'jumlah' => (float)$_POST['jumlah'],
                        'satuan' => $_POST['satuan'],
                        'harga_per_satuan' => (float)$_POST['harga_per_satuan'],
                        'keterangan' => $_POST['keterangan']
                    ];
                    if($stokBahanModel->updateStokBahan($id, $data)) {
                        $success = true;
                        $message = 'Bahan berhasil diperbarui!';
                    }
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    if($stokBahanModel->deleteStokBahan($id)) {
                        $success = true;
                        $message = 'Bahan berhasil dihapus!';
                    }
                    break;

                case 'adjust':
                    $id = $_POST['id'];
                    $jumlah = (float)$_POST['jumlah_adjust'];
                    if ($_POST['tipe_adjust'] === 'kurang') {
                        $jumlah = -$jumlah;
                    }
                    if($stokBahanModel->updateJumlahStok($id, $jumlah)) {
                        $success = true;
                        $message = 'Stok berhasil disesuaikan!';
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
        
        header('Location: stok_bahan.php');
        exit;
    }
}

// Ambil semua stok bahan dan konversi ke array
$stokBahan = iterator_to_array($stokBahanModel->getAllStokBahan());

// Hitung total nilai stok
$totalNilaiStok = 0;
$totalJenisBahan = count($stokBahan);

foreach ($stokBahan as $sb) {
    $totalNilaiStok += $sb->jumlah * $sb->harga_per_satuan;
}
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
    background: linear-gradient(45deg, #4CAF50, #45a049);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.table th {
    font-weight: 600;
    color: #495057;
}
.badge {
    padding: 0.5em 1em;
}
.modal-content {
    border: none;
    border-radius: 15px;
}
.modal-header {
    border-radius: 15px 15px 0 0;
    background: #f8f9fa;
}
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.6rem 1rem;
}
.form-control:focus, .form-select:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
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
                <h4 class="mb-3">Total Nilai Stok</h4>
                <h2 class="mb-0">Rp <?= number_format($totalNilaiStok, 0, ',', '.') ?></h2>
                <small>Total <?= $totalJenisBahan ?> jenis bahan</small>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Data Stok Bahan</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStokBahanModal">
                <i class="fas fa-plus me-2"></i>Tambah Bahan
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Bahan</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Harga/Satuan</th>
                            <th>Total Nilai</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stokBahan as $sb): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($sb->nama_bahan) ?></div>
                            </td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($sb->jenis_bahan) ?></span></td>
                            <td class="fw-bold <?= $sb->jumlah < 10 ? 'text-danger' : '' ?>">
                                <?= number_format($sb->jumlah, 1) ?>
                            </td>
                            <td><?= htmlspecialchars($sb->satuan) ?></td>
                            <td>Rp <?= number_format($sb->harga_per_satuan, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($sb->jumlah * $sb->harga_per_satuan, 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($sb->keterangan) ?></td>
                            <td>
                                <div class="action-buttons text-center">
                                    <button class="btn btn-success adjust-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#adjustStokModal"
                                            data-id="<?= $sb->_id ?>"
                                            data-nama="<?= htmlspecialchars($sb->nama_bahan) ?>"
                                            data-jumlah="<?= $sb->jumlah ?>"
                                            data-satuan="<?= htmlspecialchars($sb->satuan) ?>">
                                        <i class="fas fa-balance-scale"></i>
                                    </button>
                                    <button class="btn btn-info edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editStokBahanModal"
                                            data-id="<?= $sb->_id ?>"
                                            data-nama="<?= htmlspecialchars($sb->nama_bahan) ?>"
                                            data-jenis="<?= htmlspecialchars($sb->jenis_bahan) ?>"
                                            data-jumlah="<?= $sb->jumlah ?>"
                                            data-satuan="<?= htmlspecialchars($sb->satuan) ?>"
                                            data-harga="<?= $sb->harga_per_satuan ?>"
                                            data-keterangan="<?= htmlspecialchars($sb->keterangan) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger delete-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteStokBahanModal"
                                            data-id="<?= $sb->_id ?>"
                                            data-nama="<?= htmlspecialchars($sb->nama_bahan) ?>">
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

<!-- Modal Tambah Stok Bahan -->
<div class="modal fade" id="addStokBahanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Stok Bahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="stok_bahan.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" name="nama_bahan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Bahan</label>
                        <input type="text" class="form-control" name="jenis_bahan" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" step="0.1" class="form-control" name="jumlah" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" class="form-control" name="satuan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga per Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="harga_per_satuan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
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

<!-- Modal Adjust Stok -->
<div class="modal fade" id="adjustStokModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-balance-scale me-2"></i>Adjust Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="stok_bahan.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="adjust">
                    <input type="hidden" name="id" id="adjustId">
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Informasi Stok:</h6>
                        <p class="mb-0">
                            Bahan: <strong><span id="adjustNamaBahan"></span></strong><br>
                            Stok saat ini: <strong><span id="adjustStokSaatIni"></span> <span id="adjustSatuan"></span></strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Adjustment</label>
                        <select class="form-select" name="tipe_adjust" required>
                            <option value="tambah">Tambah Stok</option>
                            <option value="kurang">Kurang Stok</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" step="0.1" class="form-control" name="jumlah_adjust" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Adjust Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Stok Bahan -->
<div class="modal fade" id="editStokBahanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Stok Bahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="stok_bahan.php" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" name="nama_bahan" id="edit_nama_bahan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Bahan</label>
                        <input type="text" class="form-control" name="jenis_bahan" id="edit_jenis_bahan" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" step="0.1" class="form-control" name="jumlah" id="edit_jumlah" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" class="form-control" name="satuan" id="edit_satuan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga per Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="harga_per_satuan" id="edit_harga" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="edit_keterangan" rows="3"></textarea>
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

<!-- Modal Delete Stok Bahan -->
<div class="modal fade" id="deleteStokBahanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Stok Bahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="stok_bahan.php" method="POST" id="deleteForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Apakah Anda yakin ingin menghapus bahan <strong><span class="bahan-nama"></span></strong>?
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
    const alerts = document.querySelectorAll('.alert:not(.alert-info)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Adjust button handler
    const adjustButtons = document.querySelectorAll('.adjust-btn');
    adjustButtons.forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            document.querySelector('#adjustId').value = data.id;
            document.querySelector('#adjustNamaBahan').textContent = data.nama;
            document.querySelector('#adjustStokSaatIni').textContent = data.jumlah;
            document.querySelector('#adjustSatuan').textContent = data.satuan;
        });
    });

    // Edit button handler
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            document.querySelector('#edit_id').value = data.id;
            document.querySelector('#edit_nama_bahan').value = data.nama;
            document.querySelector('#edit_jenis_bahan').value = data.jenis;
            document.querySelector('#edit_jumlah').value = data.jumlah;
            document.querySelector('#edit_satuan').value = data.satuan;
            document.querySelector('#edit_harga').value = data.harga;
            document.querySelector('#edit_keterangan').value = data.keterangan;
        });
    });

    // Delete button handler
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            document.querySelector('#delete_id').value = data.id;
            document.querySelector('.bahan-nama').textContent = data.nama;
        });
    });
});
</script>