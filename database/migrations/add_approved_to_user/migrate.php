<?php
require_once __DIR__ . "/../../database.php";
require_once __DIR__ . "/../../../config.php";

load_config(".env");

$db_name = $_ENV['DB_NAME'];
$database = new Database();
$db_con = $database->connection();

$db_con->exec(<<<CT
ALTER TABLE Users
ADD COLUMN approved BOOLEAN;
CT);
?>