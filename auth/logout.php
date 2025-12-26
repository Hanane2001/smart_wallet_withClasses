<?php
require_once '../Classes/User.php';

$user = new User();
if ($user->logout()) {
    header("Location: login.php?message=logout");
    exit();
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>