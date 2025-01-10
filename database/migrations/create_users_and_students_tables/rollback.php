<?php
require_once __DIR__ . "/../../index.php";

$database->connection()->query(<<<DT
DROP TABLE Users;
DROP TABLE Students;
DT
);
?>