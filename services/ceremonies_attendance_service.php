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

    public function update_many_ceremony_attendances($ceremony_attendances)
    {
        if (empty($ceremony_attendances))
        {
            throw new Exception("Ceremony attendances should not be empty when updating them!");
        }

        // Delete all the ceremony attendances first
        $ceremony_id = $ceremony_attendances[0]->to_array()["ceremony_id"];
        $this->delete_ceremony_attendances_by_id($ceremony_id);

        // Now insert the new ceremony attendances
        $this->insert_many_ceremony_attendances($ceremony_attendances);
    }

    public function get_ceremony_special_people_info($ceremony_id)
    {
        $select_query = <<<IQ
        SELECT student_fn, speach_status, responsibility_status FROM Ceremony_Attendance
        WHERE (ceremony_id = :ceremony_id) 
            AND (
                    (speach_status != "declined" AND speach_status != "none") OR
                    (responsibility_status NOT LIKE '%declined%' AND responsibility_status != "none")
                )
        IQ;

        return parent::find_all_with_query_map($select_query, ["ceremony_id" => $ceremony_id], 
                    function ($row) 
                    { 
                        return [
                            "student_fn" => $row["student_fn"],
                            "speach_status" => SpeachStatus::tryFrom($row["speach_status"]),
                            "responsibility_status" => ResponsibilityStatus::tryFrom($row["responsibility_status"]),
                        ];
                    });
    }

    private function delete_ceremony_attendances_by_id($ceremony_id)
    {
        $delete_query = <<<IQ
        DELETE FROM Ceremony_Attendance
        WHERE ceremony_id = :ceremony_id;
        IQ;

        return parent::delete_with_query($delete_query, ["ceremony_id" => $ceremony_id]);
    }
}
?>