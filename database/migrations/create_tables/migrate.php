<?php
require_once __DIR__ . "/../../index.php";

$db_name = $_ENV['DB_NAME'];
$db_con = $database->connection();

$db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
$db_con->query(<<<CT
CREATE TABLE IF NOT EXISTS Users (
    id INT NOT NULL auto_increment PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(30),
    username VARCHAR(50) UNIQUE NOT NULL,
    role ENUM('Student', 'Administrator', 'Admin') NOT NULL
);

CREATE TABLE IF NOT EXISTS Students (
    fn VARCHAR(10) NOT NULL PRIMARY KEY,
    degree ENUM('bachelor', 'master', 'doctor') NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    graduation_year INT NOT NULL,
    grade DOUBLE NOT NULL,
    user_id INT,
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES Users(id)
);

CREATE TABLE IF NOT EXISTS Clothes (
    id INT NOT NULL auto_increment PRIMARY KEY,
    student_fn VARCHAR(10),
    CONSTRAINT fk_student_fn FOREIGN KEY (student_fn) REFERENCES Students(fn)
);
CT);

?>