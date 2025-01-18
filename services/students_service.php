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
    function update_user_id($fn, $user_id)
    {
        $update_query = <<<IQ
            UPDATE Students
            SET user_id=:user_id
            WHERE fn = :fn;
        IQ;

        $data = ["fn" => strval($fn), "user_id" => strval($user_id)];
        parent::update_with_query($update_query, $data);
    }
    function get_student_by_fn($fn)
    {
        $query = <<<IQ
            SELECT * FROM Students WHERE FN=:FN
        IQ;

        $data = ["FN" => strval($fn)];
        return parent::get_with_query($query, $data);
    }

    function find_all()
    {
        $find_query = "SELECT * FROM Students";
        return parent::find_all_with_query($find_query);
    }
}
?>