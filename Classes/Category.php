<?php
require_once 'Database.php';

class Category {
    private $conn;
    private $id;
    private $name;
    private $type;
    private $userId;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }
    
    public function create($name, $type, $userId = null) {
        $stmt = $this->conn->prepare("INSERT INTO categories (nameCat, typeCat, user_id) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $type, $userId]);
    }
    
    public function getAll($type = null, $userId = null) {
        $sql = "SELECT * FROM categories WHERE user_id IS NULL";
        $params = [];
        
        if ($userId) {
            $sql .= " OR user_id = ?";
            $params[] = $userId;
        }
        
        if ($type) {
            $sql .= " AND typeCat = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY nameCat";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE idCat = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByType($type, $userId = null) {
        $sql = "SELECT * FROM categories WHERE typeCat = ? AND (user_id IS NULL";
        $params = [$type];
        
        if ($userId) {
            $sql .= " OR user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= ") ORDER BY nameCat";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function update($id, $name, $type) {
        $stmt = $this->conn->prepare("UPDATE categories SET nameCat = ?, typeCat = ? WHERE idCat = ?");
        return $stmt->execute([$name, $type, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE idCat = ?");
        return $stmt->execute([$id]);
    }
}