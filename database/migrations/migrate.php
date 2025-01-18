<?php
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../../config.php";

load_config(".env");

$db_name = $_ENV['DB_NAME'];
$database = new Database();
$db_con = $database->connection();

$db_con->exec(<<<CT
CREATE TABLE IF NOT EXISTS Users (
    id INT auto_increment PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(256) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    role ENUM('student', 'administrator', 'admin') NOT NULL
);
CT);

$db_con->exec(<<<CT
CREATE TABLE IF NOT EXISTS Students (
    fn VARCHAR(10) PRIMARY KEY,
    degree ENUM('bachelor', 'master', 'doctor') NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    graduation_year INT NOT NULL,
    grade DOUBLE NOT NULL,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES Users(id)
);
CT);

$db_con->exec(<<<CT
CREATE TABLE IF NOT EXISTS Clothes (
    id INT auto_increment PRIMARY KEY,
    size VARCHAR(20) NOT NULL,

    student_fn VARCHAR(10),
    FOREIGN KEY (student_fn) REFERENCES Students(fn)
);
CT);

$db_con->exec(<<<CT
CREATE TABLE IF NOT EXISTS Ceremony (
    id INT auto_increment PRIMARY KEY,
    date DATETIME NOT NULL
);
CT);

$db_con->exec(<<<CT
CREATE TABLE IF NOT EXISTS Ceremony_Attendance (
    ceremony_id INT NOT NULL,
    FOREIGN KEY (ceremony_id) REFERENCES Ceremony(id),

    student_fn VARCHAR(10) NOT NULL,
    FOREIGN KEY (student_fn) REFERENCES Students(fn),

    PRIMARY KEY (ceremony_id, student_fn),

    accepted BOOLEAN,
    speach_status ENUM('none', 'waiting', 'declined', 'accepted') NOT NULL,

    boss_status ENUM(
    'none', 
    'waiting_robes', 'waiting_signatures', 'waiting_diplomas', 
    'accepted_robes', 'accepted_signatures', 'accepted_diplomas',
    'declined_robes', 'declined_signatures', 'declined_diplomas') 
);
CT);

?>