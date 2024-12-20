<?php
// Pastikan composer autoload dimuat
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    die('Composer dependencies not found. Please run: composer require mongodb/mongodb');
}

try {
    // Konfigurasi MongoDB
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    
    // Gunakan database yang sudah ada
    $database = $mongoClient->manajemen_penjahit;
    
    // Collections dengan nama yang konsisten
    $collections = [
        'users' => $database->users,
        'pelanggan' => $database->pelanggan,
        'pesanan' => $database->pesanan,
        'stok_bahan' => $database->stok_bahan
    ];
    
    // Buat variabel global untuk setiap collection
    $usersCollection = $collections['users'];
    $pelangganCollection = $collections['pelanggan'];
    $pesananCollection = $collections['pesanan'];
    $stokCollection = $collections['stok_bahan'];
    
    // Buat indeks yang diperlukan jika belum ada
    try {
        $collections['users']->createIndex(['username' => 1], ['unique' => true]);
        $collections['users']->createIndex(['email' => 1], ['unique' => true]);
    } catch (Exception $e) {}
    
    try {
        $collections['pelanggan']->createIndex(['user_id' => 1]);
        $collections['pelanggan']->createIndex(['nama' => 1]);
        $collections['pelanggan']->createIndex(['telepon' => 1]);
    } catch (Exception $e) {}
    
    try {
        $collections['pesanan']->createIndex(['user_id' => 1]);
        $collections['pesanan']->createIndex(['pelanggan_id' => 1]);
        $collections['pesanan']->createIndex(['status' => 1]);
        $collections['pesanan']->createIndex(['tanggal_pesan' => 1]);
        $collections['pesanan']->createIndex(['deadline' => 1]);
    } catch (Exception $e) {}
    
    try {
        $collections['stok_bahan']->createIndex(['user_id' => 1]);
        $collections['stok_bahan']->createIndex(['nama_bahan' => 1]);
        $collections['stok_bahan']->createIndex(['jenis' => 1]);
    } catch (Exception $e) {}
    
} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Error connecting to database: " . $e->getMessage() . 
        "<br>Pastikan: <br>" .
        "1. MongoDB sudah terinstall dan running<br>" .
        "2. Port 27017 tidak digunakan aplikasi lain<br>" .
        "3. PHP MongoDB extension sudah terinstall<br>" .
        "4. Database 'manajemen_penjahit' sudah dibuat di MongoDB<br>" .
        "5. Composer dependencies sudah diinstall dengan: composer require mongodb/mongodb");
}
?> 