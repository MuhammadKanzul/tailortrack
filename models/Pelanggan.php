<?php
require_once __DIR__ . '/../config/database.php';

class Pelanggan {
    private $collection;
    
    public function __construct($collection) {
        $this->collection = $collection;
    }
    
    public function getAllPelanggan() {
        try {
            return $this->collection->find([], ['sort' => ['nama' => 1]]);
        } catch (Exception $e) {
            error_log("Error in getAllPelanggan: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPelangganById($id) {
        try {
            return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        } catch (Exception $e) {
            error_log("Error in getPelangganById: " . $e->getMessage());
            return null;
        }
    }
    
    public function createPelanggan($data) {
        try {
            $data['created_at'] = new MongoDB\BSON\UTCDateTime();
            $result = $this->collection->insertOne($data);
            return $result->getInsertedId();
        } catch (Exception $e) {
            error_log("Error in createPelanggan: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePelanggan($id, $data) {
        try {
            $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
            $result = $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in updatePelanggan: " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePelanggan($id) {
        try {
            $result = $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            error_log("Error in deletePelanggan: " . $e->getMessage());
            return false;
        }
    }
    
    public function searchPelanggan($keyword) {
        try {
            $filter = [
                '$or' => [
                    ['nama' => ['$regex' => $keyword, '$options' => 'i']],
                    ['telepon' => ['$regex' => $keyword, '$options' => 'i']],
                    ['alamat' => ['$regex' => $keyword, '$options' => 'i']]
                ]
            ];
            return $this->collection->find($filter, ['sort' => ['nama' => 1]]);
        } catch (Exception $e) {
            error_log("Error in searchPelanggan: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPelangganCount() {
        try {
            return $this->collection->countDocuments();
        } catch (Exception $e) {
            error_log("Error in getPelangganCount: " . $e->getMessage());
            return 0;
        }
    }
} 