<?php
require __DIR__ . '/../config.php';

class Database {
    private $connection;

    public function __construct() {
        $this->connection = new PDO("mysql:host={$_ENV["DB_HOST"]};dbname=phpmyadmin", $_ENV["DB_USER"]);
        $this->connection->query("CREATE DATABASE IF NOT EXISTS {$_ENV["DB_NAME"]}; USE {$_ENV["DB_NAME"]}");
    }

    public function connection()
    {
        return $this->connection;
    }
}

$database = new Database();
?>
