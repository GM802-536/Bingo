<?php

class Connect
{
    private $host = "127.0.0.1";
    private $db_name = "bingo_db";
    private $username = "bingo_user";
    private $password = "bingo123";
    private static $conn;

    private function __construct()
    {
    }

    public static function getConnection()
    {
        if (self::$conn === null) {
            try {

                $connect = new Connect();
                self::$conn = new PDO(
                    "mysql:host=127.0.0.1;port=3308;dbname=" . $connect->db_name,
                    $connect->username,
                    $connect->password
                );

                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->exec("SET NAMES utf8mb4");

            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}