<?php
require_once __DIR__ . '/../config/database.php';

class StokBahan {
    private $collection;
    
    public function __construct($collection) {
        $this->collection = $collection;
    }
    
    public function getAllStokBahan() {
        try {
            return $this->collection->find([], [
                'sort' => ['nama_bahan' => 1]
            ]);
        } catch (Exception $e) {
            error_log("Error in getAllStokBahan: " . $e->getMessage());
            return [];
        }
    }
    
    public function getStokBahanById($id) {
        try {
            return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        } catch (Exception $e) {
            error_log("Error in getStokBahanById: " . $e->getMessage());
            return null;
        }
    }
    
    public function createStokBahan($data) {
        try {
            $data['created_at'] = new MongoDB\BSON\UTCDateTime();
            $data['jumlah'] = (float)$data['jumlah'];
            $data['harga_satuan'] = (float)$data['harga_satuan'];
            $data['total_nilai'] = $data['jumlah'] * $data['harga_satuan'];
            
            $result = $this->collection->insertOne($data);
            return $result->getInsertedId();
        } catch (Exception $e) {
            error_log("Error in createStokBahan: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateStokBahan($id, $data) {
        try {
            $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
            if (isset($data['jumlah']) && isset($data['harga_satuan'])) {
                $data['jumlah'] = (float)$data['jumlah'];
                $data['harga_satuan'] = (float)$data['harga_satuan'];
                $data['total_nilai'] = $data['jumlah'] * $data['harga_satuan'];
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in updateStokBahan: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteStokBahan($id) {
        try {
            $result = $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in deleteStokBahan: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateJumlahStok($id, $jumlah) {
        try {
            $stok = $this->getStokBahanById($id);
            if (!$stok) return false;
            
            $newJumlah = (float)$stok['jumlah'] + (float)$jumlah;
            $totalNilai = $newJumlah * (float)$stok['harga_satuan'];
            
            $result = $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'jumlah' => $newJumlah,
                    'total_nilai' => $totalNilai,
                    'updated_at' => new MongoDB\BSON\UTCDateTime()
                ]]
            );
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in updateJumlahStok: " . $e->getMessage());
            return false;
        }
    }
    
    public function searchStokBahan($keyword) {
        try {
            return $this->collection->find([
                '$or' => [
                    ['nama_bahan' => ['$regex' => $keyword, '$options' => 'i']],
                    ['jenis' => ['$regex' => $keyword, '$options' => 'i']],
                    ['keterangan' => ['$regex' => $keyword, '$options' => 'i']]
                ]
            ], ['sort' => ['nama_bahan' => 1]]);
        } catch (Exception $e) {
            error_log("Error in searchStokBahan: " . $e->getMessage());
            return [];
        }
    }
    
    public function getStokBahanByJenis($jenis) {
        try {
            return $this->collection->find(
                ['jenis' => $jenis],
                ['sort' => ['nama_bahan' => 1]]
            );
        } catch (Exception $e) {
            error_log("Error in getStokBahanByJenis: " . $e->getMessage());
            return [];
        }
    }
    
    public function getStokBahanCount() {
        try {
            return $this->collection->countDocuments();
        } catch (Exception $e) {
            error_log("Error in getStokBahanCount: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getTotalNilaiStok() {
        try {
            $result = $this->collection->aggregate([
                ['$group' => [
                    '_id' => null,
                    'total' => ['$sum' => '$total_nilai']
                ]]
            ])->toArray();
            
            return count($result) > 0 ? $result[0]['total'] : 0;
        } catch (Exception $e) {
            error_log("Error in getTotalNilaiStok: " . $e->getMessage());
            return 0;
        }
    }
} 