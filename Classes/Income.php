<?php
require '../Classes/Database.php';

class Incomes{
    private $conn;

    public function __construct(){
        $this->conn = Database::connect();
    }

    public function addIncome(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = $_POST['amountIn'] ?? '';
            $date = $_POST['dateIn'] ?? '';
            $description = $_POST['descriptionIn'] ?? '';

            if (!empty($amount) && !empty($date)) {
                $amount = floatval($amount);
                $date = $conn->real_escape_string($date);
                $description = $conn->real_escape_string($description);
                
                $stmt = $conn->prepare("INSERT INTO incomes (amountIn, dateIn, descriptionIn) VALUES (?, ?, ?)");
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
        } else {
            header("Location: ../incomes/list.php");
        }
    }

    // public function updateIncome(){

    // }
    // public function deleteIncome(){

    // }
    // public function getMonthlyTotal(){

    // }
}
Database::closeConnection();
?>