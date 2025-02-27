<?php

namespace App\Database;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null; // Change type to Database
    private PDO $pdo;

    private function __construct() {
        $host = 'database.cc.localhost';
        $dbname = 'course_catalog';
        $user = 'test_user';
        $pass = 'test_password';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get the singleton instance of Database.
     *
     * @return Database
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get the PDO instance.
     *
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->pdo;
    }
}