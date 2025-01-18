<?php
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../../config.php";

load_config(".env");

$db_name = $_ENV['DB_NAME'];
$database = new Database();
$db_con = $database->connection();

$db_con->exec('DROP TABLE IF EXISTS Ceremony_Attendance;');
$db_con->exec('DROP TABLE IF EXISTS Clothes;');
$db_con->exec('DROP TABLE IF EXISTS Students;');
$db_con->exec('DROP TABLE IF EXISTS Users;');
$db_con->exec('DROP TABLE IF EXISTS Ceremony;');

?>