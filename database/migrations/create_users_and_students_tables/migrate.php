<?php
require_once __DIR__ . "/../../index.php";

$database->connection()->query(<<<CT
USE alumnihub;

CREATE TABLE Users (
    id INT NOT NULL auto_increment PRIMARY KEY,
    email NVARCHAR(100) UNIQUE NOT NULL,
    password NVARCHAR(30),
    username NVARCHAR(50) UNIQUE NOT NULL,
    role ENUM('Student', 'Administrator', 'Admin') NOT NULL
)
CT);

$database->connection()->query(<<<CT
CREATE TABLE Students (
    fn CHAR(10) NOT NULL PRIMARY KEY,
    degree ENUM('bachelor', 'master', 'doctor') NOT NULL,
    fullname NVARCHAR(100) NOT NULL,
    graduation_year INT NOT NULL,
    grade DOUBLE NOT NULL,
    user_id INT REFERENCES Users(id)
)
CT);
?>