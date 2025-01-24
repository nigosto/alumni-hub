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

        // Delete all the existing ceremony attendances which match the student_fn and ceremony_id
        $this->delete_ceremony_attendances($ceremony_attendances);

        // Now insert the new ceremony attendances
        $this->insert_many_ceremony_attendances($ceremony_attendances);
    }

    public function update_all_ceremony_attendances($ceremony_attendances)
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
    
    // Excludes people who have declined everything or are not special 
    public function get_ceremony_special_people_info($ceremony_id)
    {
        $select_query = <<<IQ
        SELECT student_fn, accepted, speach_status, responsibility_status FROM Ceremony_Attendance
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
                            "accepted" => $row["accepted"],
                            "speach_status" => SpeachStatus::tryFrom($row["speach_status"]),
                            "responsibility_status" => ResponsibilityStatus::tryFrom($row["responsibility_status"]),
                        ];
                    });
    }

    public function get_ceremony_declined_special_or_ordinary_people_info($ceremony_id)
    {
        $select_query = <<<IQ
        SELECT student_fn, accepted, speach_status, responsibility_status FROM Ceremony_Attendance
        WHERE (ceremony_id = :ceremony_id) 
          AND (
                (speach_status = "declined" AND responsibility_status LIKE '%declined%') OR
                (speach_status = "declined" AND responsibility_status = "none") OR
                (speach_status = "none"     AND responsibility_status LIKE '%declined%') OR
                (speach_status = "none"     AND responsibility_status = "none")
              );
        IQ;

        return parent::find_all_with_query_map($select_query, ["ceremony_id" => $ceremony_id], 
                    function ($row) 
                    { 
                        return [
                            "student_fn" => $row["student_fn"],
                            "accepted" => $row["accepted"],
                            "speach_status" => SpeachStatus::tryFrom($row["speach_status"]),
                            "responsibility_status" => ResponsibilityStatus::tryFrom($row["responsibility_status"]),
                        ];
                    });
    }

    private function delete_ceremony_attendances($ceremony_attendances)
    {
        $delete_query = <<<IQ
        DELETE FROM Ceremony_Attendance
        WHERE (ceremony_id = ?) 
            AND (student_fn IN (
        IQ . implode(", ", array_fill(0, count($ceremony_attendances), "?")) . "));";

        $ceremony_attendances_asoc = array_map(function ($attendance) { return $attendance->to_array(); }, array_values($ceremony_attendances));
        $ceremony_id = $ceremony_attendances_asoc[0]["ceremony_id"];
        $query_params = [$ceremony_id, ...array_map(function ($attendance) { return $attendance["student_fn"]; }, $ceremony_attendances_asoc)];

        return parent::delete_with_query($delete_query, $query_params);
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