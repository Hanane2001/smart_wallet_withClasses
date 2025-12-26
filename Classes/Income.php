<?php
require_once 'Database.php';

class Income {
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
        $stmt = $this->conn->prepare("INSERT INTO incomes (amountIn, dateIn, descriptionIn, user_id, category_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$amount, $date, $description, $userId, $categoryId]);
    }

    public function getAll($userId, $limit = null) {
        $sql = "SELECT i.*, c.nameCat as category_name 
                FROM incomes i 
                LEFT JOIN categories c ON i.category_id = c.idCat 
                WHERE i.user_id = ? 
                ORDER BY i.dateIn DESC, i.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId = null) {
        $sql = "SELECT i.*, c.nameCat as category_name 
                FROM incomes i 
                LEFT JOIN categories c ON i.category_id = c.idCat 
                WHERE i.idIn = ?";
        
        $params = [$id];
        
        if ($userId) {
            $sql .= " AND i.user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
 
    public function getByCategory($categoryId, $userId) {
        $stmt = $this->conn->prepare(
            "SELECT i.*, c.nameCat as category_name 
             FROM incomes i 
             LEFT JOIN categories c ON i.category_id = c.idCat 
             WHERE i.category_id = ? AND i.user_id = ? 
             ORDER BY i.dateIn DESC"
        );
        $stmt->execute([$categoryId, $userId]);
        return $stmt->fetchAll();
    }
 
    public function update($id, $amount, $date, $description, $categoryId, $userId = null) {
        $sql = "UPDATE incomes SET amountIn = ?, dateIn = ?, descriptionIn = ?, category_id = ? WHERE idIn = ?";
        $params = [$amount, $date, $description, $categoryId, $id];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id, $userId = null) {
        $sql = "DELETE FROM incomes WHERE idIn = ?";
        $params = [$id];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function getTotal($userId, $month = null, $year = null) {
        $sql = "SELECT SUM(amountIn) as total FROM incomes WHERE user_id = ?";
        $params = [$userId];
        
        if ($month && $year) {
            $sql .= " AND MONTH(dateIn) = ? AND YEAR(dateIn) = ?";
            $params[] = $month;
            $params[] = $year;
        } elseif ($month) {
            $sql .= " AND MONTH(dateIn) = ? AND YEAR(dateIn) = YEAR(CURDATE())";
            $params[] = $month;
        } elseif ($year) {
            $sql .= " AND YEAR(dateIn) = ?";
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
            "SELECT MONTH(dateIn) as month, SUM(amountIn) as total 
             FROM incomes 
             WHERE user_id = ? AND YEAR(dateIn) = ? 
             GROUP BY MONTH(dateIn) 
             ORDER BY month"
        );
        $stmt->execute([$userId, $year]);
        return $stmt->fetchAll();
    }
}