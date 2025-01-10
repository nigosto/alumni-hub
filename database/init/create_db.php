<?php

$conn = new PDO("mysql:host=localhost;dbname=phpmyadmin", "root");
$conn->query('CREATE DATABASE alumnihub') or die('failed');

$conn->query(<<<CT
use alumnihub;

CREATE TABLE Users (
    id INT NOT NULL auto_increment PRIMARY KEY,
    email NVARCHAR(100) UNIQUE,
    password NVARCHAR(30) UNIQUE,
    username NVARCHAR(50) UNIQUE,
    role ENUM('Student', 'Administrator', 'Admin')
)
CT);

$conn->query(<<<CT
CREATE TABLE Students
(
    fn NVARCHAR(10) PRIMARY KEY,
    degree ENUM('bachelor', 'master'),
    fullname NVARCHAR(100),
    graduation_year INT,
    grade DOUBLE,
    user_id INT REFERENCES Users(id)
)
CT);

?>