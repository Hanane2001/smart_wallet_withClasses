<?php
require_once __DIR__ .'/../Classes/User.php';
function checkAuth() {
    if (!User::isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    return $_SESSION['user_id'];
}

function redirectIfLoggedIn() {
    if (User::isLoggedIn()) {
        header("Location: ../dashboard.php");
        exit();
    }
}