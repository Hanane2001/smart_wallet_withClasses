<?php
session_start();

class Database {

    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_NAME = 'Smart_Wallet_Classe';

    private static $db = null;

    public function __construct() {
        if (self::$db === null) {
            self::$db = new mysqli(self::DB_HOST,self::DB_USER,self::DB_PASS,self::DB_NAME);

            if (self::$db->connect_error) {
                die("Erreur de connexion : " . self::$db->connect_error);
            }

            self::$db->set_charset("utf8mb4");
        }
    }

    public static function connect() {
        if (self::$db === null) {
            new self();
        }
        return self::$db;
    }

    public static function closeConnection() {
        if (self::$db !== null) {
            self::$db->close();
            self::$db = null;
        }
    }
}
