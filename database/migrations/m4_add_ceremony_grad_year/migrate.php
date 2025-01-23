<?php
require_once __DIR__ . "/../../database.php";
require_once __DIR__ . "/../../../config.php";

load_config(".env");

$db_name = $_ENV['DB_NAME'];
$database = new Database();
$db_con = $database->connection();

$db_con->exec(<<<CT
ALTER TABLE Ceremony
ADD COLUMN graduation_year INT;
CT);

$db_con->exec(<<<CT
UPDATE Ceremony
SET graduation_year = 2025;
CT);

$db_con->exec(<<<CT
ALTER TABLE Ceremony MODIFY COLUMN graduation_year INT NOT NULL;
CT);
?>