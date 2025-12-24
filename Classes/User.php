<?php
require '../Classes/Database.php';

class Users {
    private $conn;
    private string $fullName;
    private string $email;
    private string $password;

    public function __construct(){
        $this->conn = Database::connect();
    }

    public function register(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->fullName = trim($_POST['fullName'] ?? '');
            $this->email = trim($_POST['email'] ?? '');
            $this->password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            $errors = [];

            if(empty($this->fullName)) {
                $errors[] = "Full name required";
            }
            if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email";
            }
            if(strlen($this->password) < 6) {
                $errors[] = "Password too short";
            }
            if($this->password !== $confirmPassword) {
                $errors[] = "Passwords not match";
            }

            $res = $this->conn->prepare("SELECT idUser FROM users WHERE email=?");
            $res->bind_param("s",$this->email);
            $res->execute();
            $res->store_result();

            if($res->num_rows > 0) {
                $errors[] = "Email exists";
            }
            $res->close();

            if(empty($errors)){
                $hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("INSERT INTO users(fullName,email,password) VALUES(?,?,?)");
                $stmt->bind_param("sss",$this->fullName,$this->email,$hash);
                $stmt->execute();

                $_SESSION['user_id'] = $stmt->insert_id;
                header("Location: login.php");
                exit;
            }
            $_SESSION['errors'] = $errors;
            header("Location: register.php");
            exit;
        }
    }

    public function login(){
        if (isset($_SESSION['user_id'])) {
            header("Location: ../dashboard.php");
            exit();
        }
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->email = trim($_POST['email'] ?? '');
            $this->password = $_POST['password'] ?? '';

            if(empty($this->email) || empty($this->password)){
                $errors[] = "Email and password are required";
            }

            if(empty($errors)){
                $std = $this->conn->prepare("SELECT idUser, fullName, email, password FROM users WHERE email = ?");
                $std->bind_param("s", $this->email);
                $std->execute();
                $result = $std->get_result();

                if($result->num_rows === 1){
                    $user = $result->fetch_assoc();
                    if(password_verify($this->password, $user['password'])){
                        $_SESSION['user_id'] = $user['idUser'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_name'] = $user['fullName'];
                        header("Location: ../dashboard.php");
                        exit();
                    } else {
                        $errors[] = "Invalid email or password";
                    }
                } else {
                    $errors[] = "Invalid email or password";
                }

                $std->close();
            }

            if(!empty($errors)){
                $_SESSION['errors'] = $errors;
                header("Location: login.php");
                exit();
            }
        }
    }

    public function logout(){
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }
}

Database::closeConnection();
?>