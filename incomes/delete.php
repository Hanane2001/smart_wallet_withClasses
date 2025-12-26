<?php
require '../Classes/Income.php';
$std = new Incomes();
$res = $std->deleteIncome($_GET['id']);
if ($res) {
    header("Location: list.php?message=income_deleted");
    exit();
} else {
    header("Location: list.php?error=delete_failed");
    exit();
}
?>