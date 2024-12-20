<?php
session_start();
require_once 'config/database.php';
require_once 'models/Pesanan.php';
require_once 'models/Pelanggan.php';

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pesananModel = new Pesanan($pesananCollection);
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
                        'pelanggan_id' => $_POST['pelanggan_id'],
                        'jenis_jahitan' => $_POST['jenis_jahitan'],
                        'deadline' => new MongoDB\BSON\UTCDateTime(strtotime($_POST['deadline']) * 1000),
                        'status' => 'Menunggu',
                        'total_harga' => (float)$_POST['total_harga'],
                        'keterangan' => $_POST['keterangan'],
                        'created_at' => new MongoDB\BSON\UTCDateTime()
                    ];
                    
                    if ($pesananModel->createPesanan($data)) {
                        $success = true;
                        $message = 'Pesanan berhasil ditambahkan';
                    }
                    break;

                case 'update':
                    $id = $_POST['id'];
                    $data = [
                        'jenis_jahitan' => $_POST['jenis_jahitan'],
                        'deadline' => new MongoDB\BSON\UTCDateTime(strtotime($_POST['deadline']) * 1000),
                        'status' => $_POST['status'],
                        'total_harga' => (float)$_POST['total_harga'],
                        'keterangan' => $_POST['keterangan'],
                        'updated_at' => new MongoDB\BSON\UTCDateTime()
                    ];
                    
                    if ($pesananModel->updatePesanan($id, $data)) {
                        $success = true;
                        $message = 'Pesanan berhasil diperbarui';
                    }
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    if ($pesananModel->deletePesanan($id)) {
                        $success = true;
                        $message = 'Pesanan berhasil dihapus';
                    }
                    break;

                case 'update_status':
                    $id = $_POST['id'];
                    $status = $_POST['status'];
                    if ($pesananModel->updateStatus($id, $status)) {
                        $success = true;
                        $message = 'Status pesanan berhasil diperbarui';
                    }
                    break;
            }
        } catch (Exception $e) {
            error_log("Error in pesanan: " . $e->getMessage());
            $message = 'Terjadi kesalahan: ' . $e->getMessage();
        }
        
        $_SESSION['flash'] = [
            'type' => $success ? 'success' : 'danger',
            'message' => $message
        ];
        
        header('Location: pesanan.php');
        exit;
    }
}

include 'includes/header.php';

// Ambil semua data pelanggan untuk dropdown
$pelangganList = iterator_to_array($pelangganModel->getAllPelanggan());

// Ambil semua pesanan dan konversi ke array
$pesananList = iterator_to_array($pesananModel->getAllPesanan());

// Hitung statistik
$totalPesanan = count($pesananList);
$totalPendapatan = 0;
$pesananMenunggu = 0;
$pesananProses = 0;
$pesananSelesai = 0;

foreach ($pesananList as $p) {
    $p = (array)$p;
    $totalPendapatan += $p['total_harga'] ?? 0;
    
    switch($p['status'] ?? '') {
        case 'Menunggu':
            $pesananMenunggu++;
            break;
        case 'Proses':
            $pesananProses++;
            break;
        case 'Selesai':
            $pesananSelesai++;
            break;
    }
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
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    color: white;
}
.stats-card.primary {
    background: linear-gradient(45deg, #6366F1, #4F46E5);
}
.stats-card.success {
    background: linear-gradient(45deg, #4CAF50, #45a049);
}
.stats-card.warning {
    background: linear-gradient(45deg, #F59E0B, #D97706);
}
.stats-card.info {
    background: linear-gradient(45deg, #3B82F6, #2563EB);
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
    border-color: #6366F1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}
.customer-info {
    display: flex;
    align-items: center;
}
.customer-avatar {
    width: 35px;
    height: 35px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    color: #495057;
    font-weight: bold;
}
.deadline {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.deadline.near {
    color: #DC2626;
}
.deadline.safe {
    color: #059669;
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
        <div class="col-md-3">
            <div class="stats-card primary">
                <div class="stats-icon">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
                <h4>Total Pesanan</h4>
                <h2><?= number_format($totalPesanan) ?></h2>
                <small>Semua pesanan</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card success">
                <div class="stats-icon">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
                <h4>Pendapatan</h4>
                <h2>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h2>
                <small>Total pendapatan</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card warning">
                <div class="stats-icon">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h4>Dalam Proses</h4>
                <h2><?= number_format($pesananMenunggu + $pesananProses) ?></h2>
                <small>Menunggu & proses</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card info">
                <div class="stats-icon">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h4>Selesai</h4>
                <h2><?= number_format($pesananSelesai) ?></h2>
                <small>Pesanan selesai</small>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Data Pesanan</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPesananModal">
                <i class="fas fa-plus me-2"></i>Tambah Pesanan
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Pelanggan</th>
                            <th>Pesanan</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Total Harga</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesananList as $p): 
                            $p = (array)$p;
                            $pelanggan = $pelangganModel->getPelangganById($p['pelanggan_id']);
                            $pelanggan = (array)$pelanggan;
                            
                            // Convert MongoDB UTCDateTime to PHP DateTime
                            $deadline = new DateTime();
                            if (isset($p['deadline'])) {
                                $deadline->setTimestamp($p['deadline']->toDateTime()->getTimestamp());
                            }
                            
                            // Get initials for avatar
                            $nama = $pelanggan['nama'] ?? '';
                            $initials = strtoupper(substr($nama, 0, 2));
                            
                            // Calculate days until deadline
                            $today = new DateTime();
                            $interval = $today->diff($deadline);
                            $daysUntil = $interval->invert ? -$interval->days : $interval->days;
                        ?>
                        <tr>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        <?= $initials ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($nama) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($pelanggan['telepon'] ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($p['jenis_jahitan'] ?? '') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($p['keterangan'] ?? '') ?></small>
                            </td>
                            <td>
                                <div class="deadline <?= $daysUntil <= 3 ? 'near' : 'safe' ?>">
                                    <i class="fas <?= $daysUntil <= 3 ? 'fa-exclamation-circle' : 'fa-calendar-alt' ?>"></i>
                                    <div>
                                        <div><?= $deadline->format('d/m/Y') ?></div>
                                        <small><?= $daysUntil ?> hari lagi</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($p['status']) {
                                        'Menunggu' => 'warning',
                                        'Proses' => 'info',
                                        'Selesai' => 'success',
                                        'Dibatalkan' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <i class="fas <?php 
                                        echo match($p['status']) {
                                            'Menunggu' => 'fa-clock',
                                            'Proses' => 'fa-spinner fa-spin',
                                            'Selesai' => 'fa-check-circle',
                                            'Dibatalkan' => 'fa-times-circle',
                                            default => 'fa-question-circle'
                                        };
                                    ?> me-1"></i>
                                    <?= htmlspecialchars($p['status'] ?? '') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">Rp <?= number_format($p['total_harga'] ?? 0, 0, ',', '.') ?></div>
                            </td>
                            <td>
                                <div class="action-buttons text-center">
                                    <button class="btn btn-info edit-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editPesananModal"
                                            data-id="<?= $p['_id'] ?>"
                                            data-jenis="<?= htmlspecialchars($p['jenis_jahitan'] ?? '') ?>"
                                            data-deadline="<?= isset($p['deadline']) ? $deadline->format('Y-m-d') : date('Y-m-d') ?>"
                                            data-status="<?= htmlspecialchars($p['status'] ?? '') ?>"
                                            data-harga="<?= $p['total_harga'] ?? 0 ?>"
                                            data-keterangan="<?= htmlspecialchars($p['keterangan'] ?? '') ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger delete-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deletePesananModal"
                                            data-id="<?= $p['_id'] ?>"
                                            data-pelanggan="<?= htmlspecialchars($nama) ?>">
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

<!-- Modal Tambah Pesanan -->
<div class="modal fade" id="addPesananModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user me-1"></i>Pelanggan
                        </label>
                        <select class="form-select" name="pelanggan_id" required>
                            <option value="">Pilih Pelanggan</option>
                            <?php foreach ($pelangganList as $pl): 
                                $pl = (array)$pl;
                            ?>
                                <option value="<?= $pl['_id'] ?>">
                                    <?= htmlspecialchars($pl['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tshirt me-1"></i>Jenis Jahitan
                        </label>
                        <input type="text" class="form-control" name="jenis_jahitan" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Deadline
                        </label>
                        <input type="date" class="form-control" name="deadline" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave me-1"></i>Total Harga
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="total_harga" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-sticky-note me-1"></i>Keterangan
                        </label>
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

<!-- Modal Edit Pesanan -->
<div class="modal fade" id="editPesananModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tshirt me-1"></i>Jenis Jahitan
                        </label>
                        <input type="text" class="form-control" name="jenis_jahitan" id="edit_jenis_jahitan" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Deadline
                        </label>
                        <input type="date" class="form-control" name="deadline" id="edit_deadline" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tasks me-1"></i>Status
                        </label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave me-1"></i>Total Harga
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="total_harga" id="edit_total_harga" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-sticky-note me-1"></i>Keterangan
                        </label>
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

<!-- Modal Delete Pesanan -->
<div class="modal fade" id="deletePesananModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Apakah Anda yakin ingin menghapus pesanan dari <strong><span id="delete_pelanggan"></span></strong>?
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
            console.log('Edit data:', data); // Debug log
            
            // Set values to form fields
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_jenis_jahitan').value = data.jenis;
            
            // Format tanggal ke YYYY-MM-DD untuk input date
            const deadline = data.deadline;
            document.getElementById('edit_deadline').value = deadline;
            
            document.getElementById('edit_status').value = data.status;
            document.getElementById('edit_total_harga').value = data.harga;
            document.getElementById('edit_keterangan').value = data.keterangan;
            
            // Debug log untuk memastikan nilai tanggal
            console.log('Deadline value:', deadline);
            console.log('Input date value:', document.getElementById('edit_deadline').value);
        });
    });

    // Tambahkan validasi form sebelum submit
    document.querySelector('#editPesananModal form').addEventListener('submit', function(e) {
        const deadline = document.getElementById('edit_deadline').value;
        if (!deadline) {
            e.preventDefault();
            alert('Mohon isi tanggal deadline');
            return false;
        }
        // Debug log saat submit
        console.log('Form submitted with deadline:', deadline);
    });

    // Delete button handler
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            document.getElementById('delete_id').value = data.id;
            document.getElementById('delete_pelanggan').textContent = data.pelanggan;
        });
    });
});
</script>