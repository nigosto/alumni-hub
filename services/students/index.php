<?php
require __DIR__ . "/../../database/index.php";

class StudentsService {
    private $connection;

    function __construct(Database $database) {
        $this->connection = $database->connection();
    }

    function insert_many($students) {
        try {
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            $insert_query = "INSERT INTO Students (fn, degree, fullname, graduation_year, grade, user_id) VALUES (:fn, :degree, :fullname, :graduation_year, :grade, :user_id)";
            $stmt = $this->connection->prepare($insert_query);
        
            foreach ($students as $student) {
                $data = $student->to_insert_array();
                $stmt->execute($data);
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

$students_service = new StudentsService($database);
?>