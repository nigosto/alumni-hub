<?php
require_once __DIR__ . "/../../database.php";
require_once __DIR__ . "/../../../config.php";

load_config(".env");

$database = new Database();
$db_con = $database->connection();

$db_con->exec('DROP TABLE IF EXISTS Requests;');
?>