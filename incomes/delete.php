<?php
require_once '../Classes/Income.php';
require_once '../auth/AuthCheck.php';

$userId = checkAuth();
$income = new Income();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php?error=no_id");
    exit();
}

$incomeData = $income->getById($id, $userId);
if (!$incomeData) {
    header("Location: list.php?error=not_found");
    exit();
}

if ($income->delete($id, $userId)) {
    header("Location: list.php?message=income_deleted");
    exit();
} else {
    header("Location: list.php?error=delete_failed");
    exit();
}
?>