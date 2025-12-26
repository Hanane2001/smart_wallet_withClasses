<?php
require_once '../Classes/Expense.php';
require_once '../auth/AuthCheck.php';

$userId = checkAuth();
$expense = new Expense();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php?error=no_id");
    exit();
}

$expenseData = $expense->getById($id, $userId);
if (!$expenseData) {
    header("Location: list.php?error=not_found");
    exit();
}

if ($expense->delete($id, $userId)) {
    header("Location: list.php?message=expense_deleted");
    exit();
} else {
    header("Location: list.php?error=delete_failed");
    exit();
}
?>