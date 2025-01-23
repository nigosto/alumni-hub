<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../models/ceremony.php";

class CeremoniesService extends DataService
{
    private $ceremonies_attendance_service;
    private $students_service;

    public function __construct(
        Database $database,
        CeremoniesAttendanceService $ceremonies_attendance_service,
        StudentsService $students_service
    ) {
        parent::__construct($database, Ceremony::class);
        $this->ceremonies_attendance_service = $ceremonies_attendance_service;
        $this->students_service = $students_service;
    }

    public function create_date_from_string($date)
    {
        $format = 'Y-m-d\TH:i';
        return DateTime::createFromFormat($format, $date);
    }

    public function insert_ceremony(
        $ceremony,
        $graduation_year,
        $speaker,
        $responsible_robes,
        $responsible_signatures,
        $responsible_diplomas
    ) {
        $this->execute_in_transaction(
            function () use ($ceremony, $graduation_year, $speaker, $responsible_robes, $responsible_signatures, $responsible_diplomas) {
                $insert_query = <<<IQ
                    INSERT INTO Ceremony (date, graduation_year) VALUES (:date, :graduation_year)
                IQ;

                // Insert ceremony
                $ceremony_id = parent::insert_with_query($insert_query, $ceremony);

                $special_ceremony_attendances = $this->get_special_ceremony_attendances(
                    $ceremony_id,
                    $speaker,
                    $responsible_robes,
                    $responsible_signatures,
                    $responsible_diplomas
                );

                $ordinary_ceremony_attendances = $this->get_ordinary_ceremony_attendances(
                    $special_ceremony_attendances,
                    $ceremony_id,
                    $graduation_year
                );

                // Insert ceremony attendances
                $ceremony_attendances = array_merge($special_ceremony_attendances, $ordinary_ceremony_attendances);
                $this->ceremonies_attendance_service->insert_many_ceremony_attendances($ceremony_attendances);
            }
        );
    }

    public function get_all_ceremony_info()
    {
        $insert_query = <<<IQ
            SELECT date, Ceremony.graduation_year, student_fn, speach_status, responsibility_status FROM Ceremony
            JOIN Ceremony_Attendance ON Ceremony.id = Ceremony_Attendance.ceremony_id
            JOIN Students ON Students.fn = Ceremony_Attendance.student_fn
            WHERE (speach_status != "declined" AND Ceremony_Attendance.speach_status != "none")
                OR(responsibility_status NOT LIKE '%declined%' AND responsibility_status != "none")
            ORDER BY Ceremony.graduation_year
        IQ;

        $map_func = function ($row) {
            return $row;
        };

        $ceremony_info_rows = parent::find_all_with_query_map($insert_query, null, $map_func);
        if (!$ceremony_info_rows) {
            return false;
        }

        $ceremony_special_people_info_func = function ($check_fn, $checked_value, $acceptable_values, $acceptable_value_type): string {
            $Unconfirmed_Status = " (Unconfirmed) ";
            $Confirmed_Status = " (Confirmed) ";

            if (in_array($acceptable_value_type::tryFrom($checked_value), $acceptable_values)) {
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
        foreach ($ceremony_info_rows as $row) {
            if (!$row || $graduation_year_current != $row["graduation_year"]) {
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

            if ($speaker_info_current === "none") {
                $speaker_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"],
                    $row["speach_status"],
                    [SpeachStatus::Waiting, SpeachStatus::Accepted],
                    SpeachStatus::class
                );
                $speaker_info_current = $speaker_info_new;
            }

            if ($responsible_robes_info_current === "none") {
                $responsible_robes_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"],
                    $row["responsibility_status"],
                    [ResponsibilityStatus::WaitingRobes, ResponsibilityStatus::AcceptedRobes],
                    ResponsibilityStatus::class
                );
                $responsible_robes_info_current = $responsible_robes_info_new;
            }
            if ($responsible_signatures_info_current === "none") {
                $responsible_signatures_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"],
                    $row["responsibility_status"],
                    [ResponsibilityStatus::WaitingSignatures, ResponsibilityStatus::AcceptedSignatures],
                    ResponsibilityStatus::class
                );
                $responsible_signatures_info_current = $responsible_signatures_info_new;
            }
            if ($responsible_diplomas_info_current === "none") {
                $responsible_diplomas_info_new = $ceremony_special_people_info_func(
                    $row["student_fn"],
                    $row["responsibility_status"],
                    [ResponsibilityStatus::WaitingDiplomas, ResponsibilityStatus::AcceptedDiplomas],
                    ResponsibilityStatus::class
                );
                $responsible_diplomas_info_current = $responsible_diplomas_info_new;
            }
        }

        return $ceremony_info_all;
    }

    private function get_special_ceremony_attendances(
        $ceremony_id,
        $speaker,
        $responsible_robes,
        $responsible_signatures,
        $responsible_diplomas
    ) {
        // The speaking student can also have a responsibility
        $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::None;
        if ($responsible_robes === $speaker) {
            $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingRobes;
        } else if ($responsible_signatures === $speaker) {
            $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingSignatures;
        } else if ($responsible_diplomas === $speaker) {
            $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingDiplomas;
        }

        $ceremony_attendances = [];
        $ceremony_attendance_speaker = new CeremonyAttendance(
            $ceremony_id,
            $speaker,
            null,
            SpeachStatus::Waiting,
            $ceremony_attendance_speaker_responsibility
        );
        array_push($ceremony_attendances, $ceremony_attendance_speaker);

        if ($ceremony_attendance_speaker_responsibility !== ResponsibilityStatus::WaitingRobes) {
            $ceremony_attendance_responsible_robes = new CeremonyAttendance(
                $ceremony_id,
                $responsible_robes,
                null,
                SpeachStatus::None,
                ResponsibilityStatus::WaitingRobes
            );
            array_push($ceremony_attendances, $ceremony_attendance_responsible_robes);
        }
        if ($ceremony_attendance_speaker_responsibility !== ResponsibilityStatus::WaitingSignatures) {
            $ceremony_attendance_responsible_signatures = new CeremonyAttendance(
                $ceremony_id,
                $responsible_signatures,
                null,
                SpeachStatus::None,
                ResponsibilityStatus::WaitingSignatures
            );
            array_push($ceremony_attendances, $ceremony_attendance_responsible_signatures);
        }
        if ($ceremony_attendance_speaker_responsibility !== ResponsibilityStatus::WaitingDiplomas) {
            $ceremony_attendance_responsible_diplomas = new CeremonyAttendance(
                $ceremony_id,
                $responsible_diplomas,
                null,
                SpeachStatus::None,
                ResponsibilityStatus::WaitingDiplomas
            );
            array_push($ceremony_attendances, $ceremony_attendance_responsible_diplomas);
        }

        return $ceremony_attendances;
    }

    private function get_ordinary_ceremony_attendances($special_ceremony_attendances, $ceremony_id, $graduation_year)
    {
        $special_students_fns = array_map(
            function ($value): string {
                return $value->to_array()["student_fn"]; },
            $special_ceremony_attendances
        );
        $ordinary_students_fns = $this->students_service->get_ordinary_students_fns_for_graduation_year($graduation_year, $special_students_fns);

        $ordinary_ceremony_attendances = [];
        foreach ($ordinary_students_fns as $ordinary_student_fn) {
            $ordinary_ceremony_attendance = new CeremonyAttendance(
                $ceremony_id,
                $ordinary_student_fn,
                null,
                SpeachStatus::None,
                ResponsibilityStatus::None
            );
            array_push($ordinary_ceremony_attendances, $ordinary_ceremony_attendance);
        }

        return $ordinary_ceremony_attendances;
    }
}
?>