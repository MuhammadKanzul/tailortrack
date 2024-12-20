    </div> <!-- Tutup main-content -->

    <!-- Footer -->
    <footer class="footer py-5 bg-light mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="mb-3">TailorTrack</h5>
                    <p class="text-muted">Sistem manajemen penjahit yang membantu Anda mengelola bisnis dengan lebih efisien dan profesional.</p>
                    <div class="social-links">
                        <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="mb-3">Menu</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="pelanggan.php">Pelanggan</a></li>
                        <li class="mb-2"><a href="pesanan.php">Pesanan</a></li>
                        <li class="mb-2"><a href="stok_bahan.php">Stok Bahan</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="mb-3">Bantuan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">FAQ</a></li>
                        <li class="mb-2"><a href="#">Panduan</a></li>
                        <li class="mb-2"><a href="#">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="mb-3">Berlangganan Newsletter</h6>
                    <p class="text-muted mb-3">Dapatkan tips dan update terbaru seputar manajemen bisnis jahit.</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email Anda">
                        <button class="btn btn-primary" type="button">Daftar</button>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> TailorTrack. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted">Made with <i class="fas fa-heart text-danger"></i> in Indonesia</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
    $(document).ready(function() {
        // Initialize Bootstrap components
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Make sure dropdowns work
        $('.dropdown-toggle').dropdown();
    });
    </script>
</body>
</html> 