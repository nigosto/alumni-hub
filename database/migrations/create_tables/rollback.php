<?php
require_once __DIR__ . "/../../index.php";

$db_name = $_ENV['DB_NAME'];
$db_con = $database->connection();

$db_con->exec(<<<CT
DROP TABLE IF EXISTS Ceremony_Attendance;
CT);

$db_con->exec(<<<CT
DROP TABLE IF EXISTS Clothes;
CT);

$db_con->exec(<<<CT
DROP TABLE IF EXISTS Students;
CT);

$db_con->exec(<<<CT
DROP TABLE IF EXISTS Users;
CT);

$db_con->exec(<<<CT
DROP TABLE IF EXISTS Ceremony;
CT);

?>