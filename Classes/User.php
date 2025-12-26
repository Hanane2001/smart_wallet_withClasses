<?php
require_once 'Database.php';

class User {
    private $conn;
    private $id;
    private $fullName;
    private $email;
    private $password;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }
    public function getId() { return $this->id; }
    public function getFullName() { return $this->fullName; }
    public function getEmail() { return $this->email; }

    public function register($fullName, $email, $password, $confirmPassword) {
        $errors = [];

        if (empty($fullName)) $errors[] = "Full name is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
        if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
        if ($password !== $confirmPassword) $errors[] = "Passwords do not match";
        if (empty($errors)) {
            $stmt = $this->conn->prepare("SELECT idUser FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already exists";
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (fullName, email, password) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$fullName, $email, $hashedPassword])) {
            $this->id = $this->conn->lastInsertId();
            $this->fullName = $fullName;
            $this->email = $email;
            
            $_SESSION['user_id'] = $this->id;
            $_SESSION['user_name'] = $fullName;
            $_SESSION['user_email'] = $email;
            
            return true;
        }
        
        return false;
    }

    public function login($email, $password) {
        $errors = [];
        
        if (empty($email) || empty($password)) {
            $errors[] = "Email and password are required";
            $_SESSION['errors'] = $errors;
            return false;
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['idUser'];
            $this->fullName = $user['fullName'];
            $this->email = $user['email'];
            
            $_SESSION['user_id'] = $this->id;
            $_SESSION['user_name'] = $this->fullName;
            $_SESSION['user_email'] = $this->email;
            
            return true;
        } else {
            $errors[] = "Invalid email or password";
            $_SESSION['errors'] = $errors;
            return false;
        }
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM users WHERE idUser = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        }
        return null;
    }

    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
}