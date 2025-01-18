<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../models/student.php";

class StudentsService extends DataService
{
    function __construct(Database $database)
    {
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

    function get_student_by_fn($fn)
    {
        $query = <<<IQ
            SELECT * FROM Students WHERE FN=:FN
        IQ;

        $data = ["FN" => strval($fn)];
        parent::get_with_query($query, $data);
    }

    function find_all()
    {
        $find_query = "SELECT * FROM Students";
        return parent::find_all_with_query($find_query);
    }
}
?>