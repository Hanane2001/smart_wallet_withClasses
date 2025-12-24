<?php
require 'Database.php';

class Expenses{
    private $conn;
    private float $amountEx;
    private string $dateEx;
    private string $descriptionEx;

    public function __construct(){
        $this->conn = Database::connect();
    }
    public function addExpense(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->amountEx = $_POST['amountEx'] ?? '';
            $this->dateEx = $_POST['dateEx'] ?? '';
            $this->descriptionEx = $_POST['descriptionEx'] ?? '';
            
            if (!empty($this->amountEx) && !empty($this->dateEx)) {
                $amount = floatval($this->amountEx);
                $date = $this->conn->real_escape_string($this->dateEx);
                $description = $this->conn->real_escape_string($this->descriptionEx);

                $stmt = $this->conn->prepare("INSERT INTO expenses (amountEx, dateEx, descriptionEx) VALUES (?, ?, ?)");
                $stmt->bind_param("dss", $amount, $date, $description);
                
                if ($stmt->execute()) {
                    header("Location: ../expenses/list.php?message=expense_added");
                } else {
                    header("Location: ../expenses/list.php?error=insert_failed");
                }
                $stmt->close();
            } else {
                header("Location: ../expenses/list.php?error=missing_fields");
            }
        }
    }
    public function AfficheExpense():mysqli_result{
        $result = $this->conn->query("SELECT * FROM expenses ORDER BY dateEx DESC");
        return $result;
    }
    public function updateExpense(){

    }
    public function deleteExpense(){

    }
    public function checkLimit(){

    }
    public function getCategoryTotal(){

    }

}
Database::closeConnection();
?>