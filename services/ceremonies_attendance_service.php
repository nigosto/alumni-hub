<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../models/ceremony_attendance.php";

class CeremoniesAttendanceService extends DataService
{
    public $students_service;
    
    function __construct(Database $database, $students_service)
    {
        parent::__construct($database, CeremonyAttendance::class);
        $this->students_service = $students_service;
    }

    public function insert_ceremony_attendance($ceremony_attendance)
    {
        $insert_query = <<<IQ
            INSERT INTO Ceremony_Attendance (ceremony_id, student_fn, accepted, speach_status, responsibility_status) 
            VALUES (:ceremony_id, :student_fn, :accepted, :speach_status, :responsibility_status)
        IQ;

        return parent::insert_with_query($insert_query, $ceremony_attendance);
    }

    
    public function insert_ceremony_ordinary_attendances($graduation_year, $special_attendants)
    {
        $ordinary_students_fns = $this->students_service->get_fns_for_graduation_year($graduation_year, $special_attendants);

        $insert_values = "(:ceremony_id, :student_fn, :accepted, :speach_status, :responsibility_status)";

        // $insert_query = <<<IQ
        //     INSERT INTO Ceremony (date) VALUES (:date)
        // IQ;

        // return parent::insert_with_query($insert_query, ["date" => $graduation_year]);
    }
}
?>