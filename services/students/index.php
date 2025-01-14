<?php
require_once __DIR__ . "/../../database/index.php";
require_once __DIR__ . "/../base.php";

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

    function get_student_by_fn($fn)
    {
        $query = <<<IQ
            SELECT * FROM Students WHERE FN=:FN
        IQ;

        $data = ["FN" => strval($fn)];
        parent::get_with_query($query, $data);
    }
}

$students_service = new StudentsService($database);
?>