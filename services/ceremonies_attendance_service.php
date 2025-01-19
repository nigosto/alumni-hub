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

    public function insert_many_ceremony_attendances($ceremony_attendances)
    {
        $placeholders = [];
        $values = [];
        foreach ($ceremony_attendances as $ceremony_attendance) {
            $placeholders[] = "(?, ?, ?, ?, ?)";
            $values = array_merge($values, array_values($ceremony_attendance->to_array()));
        }

        $insert_query = <<<IQ
        INSERT INTO Ceremony_Attendance (ceremony_id, student_fn, accepted, speach_status, responsibility_status)  
        VALUES 
        IQ . implode(", ", $placeholders);

        return parent::insert_many_bulk_query($insert_query, $values);
    }
}
?>