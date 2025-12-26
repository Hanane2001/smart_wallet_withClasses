<?php
require 'Database.php';

class Incomes{
    private $conn;
    private float $amountIn;
    private string $dateIn;
    private string $descriptionIn;

    public function __construct(){
        $this->conn = Database::connect();
    }

    public function addIncome(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->amountIn = $_POST['amountIn'] ?? '';
            $this->dateIn = $_POST['dateIn'] ?? '';
            $this->descriptionIn = $_POST['descriptionIn'] ?? '';

            if (!empty($this->amountIn) && !empty($this->dateIn)) {
                $amount = floatval($this->amountIn);
                $date = $this->conn->real_escape_string($this->dateIn);
                $description = $this->conn->real_escape_string($this->descriptionIn);
                
                $stmt = $this->conn->prepare("INSERT INTO incomes (amountIn, dateIn, descriptionIn) VALUES (?, ?, ?)");
                $stmt->bind_param("dss", $amount, $date, $description);
                
                if ($stmt->execute()) {
                    header("Location: ../incomes/list.php?message=income_added");
                } else {
                    header("Location: ../incomes/list.php?error=insert_failed");
                }
                $stmt->close();
            } else {
                header("Location: ../incomes/list.php?error=missing_fields");
            }
        }
    }

    public function AfficheIncome():mysqli_result{
        $result = $this->conn->query("SELECT * FROM incomes ORDER BY dateIn DESC");
        return $result;
    }

    public function updateIncome(int $id,float $amountIn,string $dateIn,string $descriptionIn): bool {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }
        if (!$id || !$amountIn || !$dateIn) {
            return false;
        }
        $stmt = $this->conn->prepare("UPDATE incomes SET amountIn = ?, dateIn = ?, descriptionIn = ? WHERE idIn = ?");
        $stmt->bind_param("dssi", $amountIn, $dateIn, $descriptionIn, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    public function UpdateIn(int $id): mysqli_result {
        $stmt = $this->conn->prepare("SELECT * FROM incomes WHERE idIn = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function deleteIncome(int $id): bool{
        if (!isset($id) || empty($id)) {
            return false;
        }
        $id = intval($id);
        $stmt = $this->conn->prepare("DELETE FROM incomes WHERE idIn = ?");
        $stmt->bind_param("i", $id);
        $success= $stmt->execute();
        $stmt->close();
        return $success;
    }
    // public function getMonthlyTotal(){

    // }
}
Database::closeConnection();
?>