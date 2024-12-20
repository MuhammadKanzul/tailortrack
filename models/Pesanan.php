<?php
require_once __DIR__ . '/../config/database.php';

class Pesanan {
    private $collection;
    
    public function __construct($collection) {
        $this->collection = $collection;
    }
    
    public function getAllPesanan() {
        try {
            return $this->collection->find([], [
                'sort' => ['tanggal_pesan' => -1]
            ]);
        } catch (Exception $e) {
            error_log("Error in getAllPesanan: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPesananById($id) {
        try {
            return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        } catch (Exception $e) {
            error_log("Error in getPesananById: " . $e->getMessage());
            return null;
        }
    }
    
    public function createPesanan($data) {
        try {
            $data['created_at'] = new MongoDB\BSON\UTCDateTime();
            $data['tanggal_pesan'] = new MongoDB\BSON\UTCDateTime();
            if (isset($data['deadline'])) {
                $data['deadline'] = new MongoDB\BSON\UTCDateTime(strtotime($data['deadline']) * 1000);
            }
            $data['pelanggan_id'] = new MongoDB\BSON\ObjectId($data['pelanggan_id']);
            
            $result = $this->collection->insertOne($data);
            return $result->getInsertedId();
        } catch (Exception $e) {
            error_log("Error in createPesanan: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePesanan($id, $data) {
        try {
            $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
            if (isset($data['deadline'])) {
                $data['deadline'] = new MongoDB\BSON\UTCDateTime(strtotime($data['deadline']) * 1000);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in updatePesanan: " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePesanan($id) {
        try {
            $result = $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in deletePesanan: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPesananByPelangganId($pelangganId) {
        try {
            return $this->collection->find(
                ['pelanggan_id' => new MongoDB\BSON\ObjectId($pelangganId)],
                ['sort' => ['tanggal_pesan' => -1]]
            );
        } catch (Exception $e) {
            error_log("Error in getPesananByPelangganId: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPesananByStatus($status) {
        try {
            return $this->collection->find(
                ['status' => $status],
                ['sort' => ['tanggal_pesan' => -1]]
            );
        } catch (Exception $e) {
            error_log("Error in getPesananByStatus: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateStatus($id, $status) {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'status' => $status,
                    'updated_at' => new MongoDB\BSON\UTCDateTime()
                ]]
            );
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in updateStatus: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPesananCount() {
        try {
            return $this->collection->countDocuments();
        } catch (Exception $e) {
            error_log("Error in getPesananCount: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getPesananCountByStatus($status) {
        try {
            return $this->collection->countDocuments(['status' => $status]);
        } catch (Exception $e) {
            error_log("Error in getPesananCountByStatus: " . $e->getMessage());
            return 0;
        }
    }
} 