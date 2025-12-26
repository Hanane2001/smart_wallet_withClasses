<?php
require '../Classes/Expense.php';
$std = new Expenses();
$res = $std->deleteExpense($_GET['id']);
if ($res) {
    header("Location: list.php?message=expense_deleted");
    exit();
} else {
    header("Location: list.php?error=delete_failed");
    exit();
}
?>