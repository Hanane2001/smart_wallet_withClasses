<?php
require_once 'Database.php';

class Expense {
    private $conn;
    private $id;
    private $amount;
    private $date;
    private $description;
    private $userId;
    private $categoryId;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }

    public function create($amount, $date, $description, $userId, $categoryId = null) {
        if (empty($amount) || empty($date) || empty($userId)) {
            return false;
        }
        
        $stmt = $this->conn->prepare("INSERT INTO expenses (amountEx, dateEx, descriptionEx, user_id, category_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$amount, $date, $description, $userId, $categoryId]);
    }

    public function getAll($userId, $limit = null) {
        $sql = "SELECT e.*, c.nameCat as category_name 
                FROM expenses e 
                LEFT JOIN categories c ON e.category_id = c.idCat 
                WHERE e.user_id = ? 
                ORDER BY e.dateEx DESC, e.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId = null) {
        $sql = "SELECT e.*, c.nameCat as category_name 
                FROM expenses e 
                LEFT JOIN categories c ON e.category_id = c.idCat 
                WHERE e.idEx = ?";
        
        $params = [$id];
        
        if ($userId) {
            $sql .= " AND e.user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    public function getByCategory($categoryId, $userId) {
        $stmt = $this->conn->prepare(
            "SELECT e.*, c.nameCat as category_name 
             FROM expenses e 
             LEFT JOIN categories c ON e.category_id = c.idCat 
             WHERE e.category_id = ? AND e.user_id = ? 
             ORDER BY e.dateEx DESC"
        );
        $stmt->execute([$categoryId, $userId]);
        return $stmt->fetchAll();
    }
    
    public function update($id, $amount, $date, $description, $categoryId, $userId = null) {
        $sql = "UPDATE expenses SET amountEx = ?, dateEx = ?, descriptionEx = ?, category_id = ? WHERE idEx = ?";
        $params = [$amount, $date, $description, $categoryId, $id];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id, $userId = null) {
        $sql = "DELETE FROM expenses WHERE idEx = ?";
        $params = [$id];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getTotal($userId, $month = null, $year = null) {
        $sql = "SELECT SUM(amountEx) as total FROM expenses WHERE user_id = ?";
        $params = [$userId];
        
        if ($month && $year) {
            $sql .= " AND MONTH(dateEx) = ? AND YEAR(dateEx) = ?";
            $params[] = $month;
            $params[] = $year;
        } elseif ($month) {
            $sql .= " AND MONTH(dateEx) = ? AND YEAR(dateEx) = YEAR(CURDATE())";
            $params[] = $month;
        } elseif ($year) {
            $sql .= " AND YEAR(dateEx) = ?";
            $params[] = $year;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
  
    public function getMonthlyTotal($userId, $year = null) {
        $year = $year ?? date('Y');
        $stmt = $this->conn->prepare(
            "SELECT MONTH(dateEx) as month, SUM(amountEx) as total 
             FROM expenses 
             WHERE user_id = ? AND YEAR(dateEx) = ? 
             GROUP BY MONTH(dateEx) 
             ORDER BY month"
        );
        $stmt->execute([$userId, $year]);
        return $stmt->fetchAll();
    }

    public function checkCategoryLimit($categoryId, $userId, $month = null, $year = null) {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        $stmt = $this->conn->prepare(
            "SELECT SUM(amountEx) as total 
             FROM expenses 
             WHERE category_id = ? AND user_id = ? 
             AND MONTH(dateEx) = ? AND YEAR(dateEx) = ?"
        );
        $stmt->execute([$categoryId, $userId, $month, $year]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getCategoryTotal($userId, $month = null, $year = null) {
        $sql = "SELECT c.nameCat, SUM(e.amountEx) as total 
                FROM expenses e 
                LEFT JOIN categories c ON e.category_id = c.idCat 
                WHERE e.user_id = ?";
        
        $params = [$userId];
        
        if ($month && $year) {
            $sql .= " AND MONTH(e.dateEx) = ? AND YEAR(e.dateEx) = ?";
            $params[] = $month;
            $params[] = $year;
        }
        
        $sql .= " GROUP BY e.category_id ORDER BY total DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}