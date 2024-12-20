<?php
class User {
    private $collection;
    
    public function __construct($collection) {
        $this->collection = $collection;
    }
    
    public function getAllUsers() {
        return $this->collection->find([]);
    }
    
    public function getUserById($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }
    
    public function getUserByUsername($username) {
        return $this->collection->findOne(['username' => $username]);
    }
    
    public function createUser($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = new MongoDB\BSON\UTCDateTime();
        return $this->collection->insertOne($data);
    }
    
    public function updateUser($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data]
        );
    }
    
    public function deleteUser($id) {
        return $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }
    
    public function verifyLogin($username, $password) {
        $user = $this->getUserByUsername($username);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }
    
    public function updateProfile($id, $data, $foto = null) {
        if ($foto) {
            // Generate nama file unik
            $foto_name = time() . '_' . $foto['name'];
            $upload_path = 'uploads/profile/';
            
            // Buat direktori jika belum ada
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            // Pindahkan file
            if (move_uploaded_file($foto['tmp_name'], $upload_path . $foto_name)) {
                $data['foto_profil'] = $foto_name;
                
                // Hapus foto lama jika ada
                $user = $this->getUserById($id);
                if (isset($user->foto_profil) && file_exists($upload_path . $user->foto_profil)) {
                    unlink($upload_path . $user->foto_profil);
                }
            }
        }
        
        $data['updated_at'] = new MongoDB\BSON\UTCDateTime();
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data]
        );
    }
} 