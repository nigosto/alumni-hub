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

    public function find_one_for_student($ceremony_id, $student_fn) {
        $query = <<<FQ
            SELECT * FROM Ceremony_Attendance 
            WHERE student_fn = :student_fn AND ceremony_id = :ceremony_id
        FQ;

        $data = ["student_fn" => $student_fn, "ceremony_id" => $ceremony_id];
        return parent::get_with_query($query, $data);
    }

    public function find_by_student_fn($student_fn)
    {
        $query = <<<FQ
            SELECT * FROM Ceremony_Attendance 
            JOIN Ceremony ON ceremony_id = id
            WHERE student_fn = :student_fn
        FQ;

        return parent::find_all_with_query_map($query, ["student_fn" => $student_fn], function ($row) {
            $row["speach_status"] = SpeachStatus::tryFrom($row["speach_status"]);
            $row["responsibility_status"] = ResponsibilityStatus::tryFrom($row["responsibility_status"]);
            return $row;   
        });
    }

    public function update_speach_status($ceremony_id, $student_fn, SpeachStatus $status) {
        $query = <<<UQ
            UPDATE `Ceremony_Attendance` 
            SET speach_status = :status
            WHERE ceremony_id = :ceremony_id AND student_fn = :student_fn
        UQ;

        $data = [
            "status" => $status->value,
            "ceremony_id" => $ceremony_id,
            "student_fn" => $student_fn
        ];
        return parent::update_with_query($query, $data);
    }

    public function update_accepted_status($ceremony_id, $student_fn, $status) {
        $query = <<<UQ
            UPDATE `Ceremony_Attendance` 
            SET accepted = :status
            WHERE ceremony_id = :ceremony_id AND student_fn = :student_fn
        UQ;

        $data = [
            "status" => intval($status),
            "ceremony_id" => $ceremony_id,
            "student_fn" => $student_fn
        ];
        return parent::update_with_query($query, $data);
    }

    public function update_responsibility_status($ceremony_id, $student_fn, ResponsibilityStatus $status) {
        $query = <<<UQ
            UPDATE `Ceremony_Attendance` 
            SET responsibility_status = :status
            WHERE ceremony_id = :ceremony_id AND student_fn = :student_fn
        UQ;

        $data = [
            "status" => $status->value,
            "ceremony_id" => $ceremony_id,
            "student_fn" => $student_fn
        ];
        return parent::update_with_query($query, $data);
    }
}
?>