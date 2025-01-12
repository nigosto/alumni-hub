<?php
require_once __DIR__ . "/../../database/index.php";
require_once __DIR__ . "/../base.php";
require_once __DIR__ . "/../../models/student.php";

class StudentsService extends BaseDataService
{
    function __construct(Database $database) {
        parent::__construct($database, Student::class);
    }

    function insert_many($students)
    {
        $insert_query = <<<IQ
            INSERT INTO Students (fn, degree, fullname, graduation_year, grade, user_id) 
            VALUES (:fn, :degree, :fullname, :graduation_year, :grade, :user_id)
        IQ;

        parent::insert_many_with_query($insert_query, $students);
    }

    function find_all() {
        $find_query = "SELECT * FROM Students";
        return parent::find_all_with_query($find_query);
    }
}

$students_service = new StudentsService($database);
?>