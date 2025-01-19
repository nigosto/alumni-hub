<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../models/ceremony.php";

class CeremoniesService extends DataService
{
    public function __construct(Database $database)
    {
        parent::__construct($database, Ceremony::class);
    }

    public function create_date_from_string($date)
    {
        $format = 'Y-m-d\TH:i';
        return DateTime::createFromFormat($format, $date);
    }

    public function insert_ceremony($ceremony)
    {
        $insert_query = <<<IQ
            INSERT INTO Ceremony (date) VALUES (:date)
        IQ;

        return parent::insert_with_query($insert_query, $ceremony);
    }

    public function get_all_ceremony_info()
    {
        $insert_query = <<<IQ
            SELECT * FROM Ceremony
            JOIN Ceremony_Attendance ON Ceremony.id = Ceremony_Attendance.ceremony_id
            JOIN Students ON Students.fn = ceremony_attendance.student_fn
            WHERE (speach_status != "declined" AND Ceremony_Attendance.speach_status != "none")
                OR(responsibility_status NOT LIKE '%declined%' AND responsibility_status != "none")
            ORDER BY graduation_year
        IQ;

        $map_func = function ($row) 
        {
            return $row;
        };

        $ceremony_info_rows = parent::find_all_with_query_map($insert_query, null, $map_func);
        if (!$ceremony_info_rows)
        {
            return false;
        }

        $ceremony_special_people_info_func = function(
            $check_fn,
            $checked_value,
            $acceptable_values,
            $acceptable_value_type
        ): string
        {
            $Unconfirmed_Status = " (Unconfirmed) ";
            $Confirmed_Status = " (Confirmed) ";

            if (in_array($acceptable_value_type::tryFrom($checked_value), $acceptable_values))
            {
                return $check_fn
                    . (str_starts_with($checked_value, "waiting") ? $Unconfirmed_Status : $Confirmed_Status);
            }

            return "none";
        };

        $ceremony_info_all = [];

        $date_current = $ceremony_info_rows[0]["date"];
        $speaker_info_current = "none";
        $responsible_robes_info_current = "none";
        $responsible_signatures_info_current = "none";
        $responsible_diplomas_info_current = "none";
        $graduation_year_current = $ceremony_info_rows[0]["graduation_year"];

        // Add dummy last val so we can add the last ceremony info

        $ceremony_info_rows[] = false;
        foreach ($ceremony_info_rows as $row)
        {
            if (!$row || $graduation_year_current != $row["graduation_year"])
            {
                // Store current ceremony info
                array_push($ceremony_info_all, [
                    "date" => $date_current,
                    "graduation_year" => $graduation_year_current,
                    "speaker" => $speaker_info_current,
                    "responsible_robes" => $responsible_robes_info_current,
                    "responsible_signatures" => $responsible_signatures_info_current,
                    "responsible_diplomas" => $responsible_diplomas_info_current,
                ]);

                // Reset current ceremony info
                $date_current = $row["date"];
                $speaker_info_current = "none";
                $responsible_robes_info_current = "none";
                $responsible_signatures_info_current = "none";
                $responsible_diplomas_info_current = "none";
                $graduation_year_current = $row["graduation_year"];
            }
            
            var_dump($row["speach_status"]);
            if ($speaker_info_current === "none")
            {
                $speaker_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"], 
                    $row["speach_status"], 
                    [SpeachStatus::Waiting, SpeachStatus::Accepted],
                SpeachStatus::class);
                $speaker_info_current = $speaker_info_new;
            }

            if ($responsible_robes_info_current === "none")
            {
                $responsible_robes_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"], 
                    $row["responsibility_status"], 
                    [ResponsibilityStatus::WaitingRobes, ResponsibilityStatus::AcceptedSignatures],
                ResponsibilityStatus::class);
                $responsible_robes_info_current = $responsible_robes_info_new;
            }
            if ($responsible_signatures_info_current === "none")
            {
                $responsible_signatures_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"], 
                    $row["responsibility_status"],
                    [ResponsibilityStatus::WaitingSignatures, ResponsibilityStatus::AcceptedSignatures],
                    ResponsibilityStatus::class);
                $responsible_signatures_info_current = $responsible_signatures_info_new;
            }
            if ($responsible_diplomas_info_current === "none")
            {
                $responsible_diplomas_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"], 
                    $row["responsibility_status"], 
                    [ResponsibilityStatus::WaitingDiplomas, ResponsibilityStatus::AcceptedDiplomas],
                    ResponsibilityStatus::class);
                $responsible_diplomas_info_current = $responsible_diplomas_info_new;
            }
        }

        return $ceremony_info_all;
    }
}
?>