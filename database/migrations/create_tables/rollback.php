<?php
require_once __DIR__ . "/../../index.php";

$db_name = $_ENV['DB_NAME'];
$db_con = $database->connection();

$db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Drop the tables
$db_con->exec("DROP TABLE IF EXISTS Clothes");
$db_con->exec("DROP TABLE IF EXISTS Students");
$db_con->exec("DROP TABLE IF EXISTS Users");
?>