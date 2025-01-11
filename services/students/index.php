<?php
require __DIR__ . "/../../database/index.php";
require __DIR__ . "/../base.php";

class StudentsService extends BaseDataService
{
    function insert_many($students)
    {
        $insert_query = <<<IQ
            INSERT INTO Students (fn, degree, fullname, graduation_year, grade, user_id) 
            VALUES (:fn, :degree, :fullname, :graduation_year, :grade, :user_id)
        IQ;

        parent::insert_many_with_query($insert_query, $students);
    }
}

$students_service = new StudentsService($database);
?>