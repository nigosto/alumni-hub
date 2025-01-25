<?php
require_once __DIR__ . "/../../database.php";
require_once __DIR__ . "/../../../config.php";

load_config(".env");

$db_name = $_ENV['DB_NAME'];
$database = new Database();
$db_con = $database->connection();

$db_con->exec(<<<CT
CREATE TABLE IF NOT EXISTS Requests (
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(id),

    student_fn VARCHAR(10) NOT NULL,
    FOREIGN KEY (student_fn) REFERENCES Students(fn),
    
    PRIMARY KEY (user_id, student_fn)
);
CT);

?>