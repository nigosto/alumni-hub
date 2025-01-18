<?php
require_once __DIR__ . "/../../database.php";
require_once __DIR__ . "/../../../config.php";

load_config(".env");

$db_name = $_ENV['DB_NAME'];
$database = new Database();
$db_con = $database->connection();

$db_con->exec(<<<CT
ALTER TABLE Ceremony_Attendance 
    CHANGE COLUMN responsibility_status boss_status ENUM(
    'none', 
    'waiting_robes', 'waiting_signatures', 'waiting_diplomas', 
    'accepted_robes', 'accepted_signatures', 'accepted_diplomas',
    'declined_robes', 'declined_signatures', 'declined_diplomas');
CT);

?>