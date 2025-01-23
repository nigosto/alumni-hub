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

    public function create_date_from_js_string($date)
    {
        $format = 'Y-m-d\TH:i';
        return DateTime::createFromFormat($format, $date);
    }

    public function insert_ceremony(
        $ceremony,
        $speaker,
        $responsible_robes,
        $responsible_signatures,
        $responsible_diplomas
    ) {
        $this->execute_in_transaction(
            function () use ($ceremony, $speaker, $responsible_robes, $responsible_signatures, $responsible_diplomas) {
                $insert_query = <<<IQ
                    INSERT INTO Ceremony (date, graduation_year) VALUES (:date, :graduation_year)
                IQ;

                $ceremony_info = $ceremony->to_array();
                unset($ceremony_info['id']);
                // Insert ceremony
                $ceremony_id = parent::insert_with_query_direct($insert_query, $ceremony_info);

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
                    $ceremony_info["graduation_year"]
                );

                // Insert ceremony attendances
                $ceremony_attendances = array_merge($special_ceremony_attendances, $ordinary_ceremony_attendances);
                $this->ceremonies_attendance_service->insert_many_ceremony_attendances($ceremony_attendances);
            }
        );
    }

    public function update_ceremony(
        $ceremony,
        $speaker,
        $responsible_robes,
        $responsible_signatures,
        $responsible_diplomas
    ) {
        $ceremony_info = $ceremony->to_array(); 
        $this->execute_in_transaction(
            function () use ($ceremony_info, $speaker, $responsible_robes, $responsible_signatures, $responsible_diplomas) {
                $ceremony_id = $ceremony_info["id"];
                $old_ceremony_info = $this->get_ceremony_simple_info_by_id($ceremony_id)->to_array();

                // If date or graduation year are changed, delete all old attendances and insert the new ones.
                if ($old_ceremony_info["date"] !==  $ceremony_info["date"] || 
                    strval($old_ceremony_info["graduation_year"]) !==  $ceremony_info["graduation_year"])
                {
                    $this->update_ceremony_entirely(
                        $ceremony_info, $speaker, $responsible_robes, $responsible_signatures, $responsible_diplomas
                    );
                    return;
                }
                // If date or graduation year are not changed, we have to update the attendances accordingly
                // Get current special people info
                $old_special_people_info = $this->ceremonies_attendance_service->get_ceremony_special_people_info($ceremony_id);

                // Get new special ceremony attendances
                $requested_special_ceremony_attendances = $this->get_special_ceremony_attendances(
                    $ceremony_info["id"],
                    $speaker,
                    $responsible_robes,
                    $responsible_signatures,
                    $responsible_diplomas
                );
                
                $new_special_ceremony_attendances = array_reduce($requested_special_ceremony_attendances, 
                function ($result, $attendance) {
                    $object_student_fn = $attendance->to_array()["student_fn"];
                    $result[$object_student_fn] = $attendance;
                    return $result;
                }, []);

                // Now, based on the differences, decide the new ceremony attendances
                foreach ($old_special_people_info as $old_info)
                {
                    $student_fn = $old_info["student_fn"];
                    if (!isset($new_special_ceremony_attendances[$student_fn]))
                    {
                        // This used to be a special student who will now be ordinary
                        // Upon creation of the ordinary attendances, his "special" statuses will be "cleared"
                        continue;
                    }
                    
                    $new_attendance = $new_special_ceremony_attendances[$student_fn];
                    
                    $new_speach_status = $new_attendance->get_speach_status();
                    $old_speach_status = $old_info["speach_status"];
                    // 1) Already accepted speach status remains accepted
                    // 2) If the old status was declined, the student will be asked again, so we don't need to do anything
                    // 3) If the old status was waiting, we don't need to do anything
                    // 3) If the old status was none, we don't need to do anything
                    if ($old_speach_status !== $new_speach_status &&
                        $old_speach_status === SpeachStatus::Accepted && 
                        $new_speach_status === SpeachStatus::Waiting)
                    {
                        $new_special_ceremony_attendances[$student_fn]->set_speach_status(SpeachStatus::Accepted);
                    }
                    
                    $new_responsibility_status = $new_attendance->get_responsibility_status();
                    $old_responsibility_status = $old_info["responsibility_status"];
                    
                    if ($old_responsibility_status !== $new_responsibility_status)
                    {
                        // 1) Old status was accepted for robes and new status is unchanged => do not invalidate the accepted status
                        if ($old_responsibility_status === ResponsibilityStatus::AcceptedRobes &&
                            $new_responsibility_status === ResponsibilityStatus::WaitingRobes)
                        {
                            $new_special_ceremony_attendances[$student_fn]->set_responsibility_status(ResponsibilityStatus::AcceptedRobes);
                        }
                        // 2) Old status was accepted for signatures and new status is unchanged => do not invalidate the accepted status
                        else if ($old_responsibility_status === ResponsibilityStatus::AcceptedSignatures &&
                                 $new_responsibility_status === ResponsibilityStatus::WaitingSignatures)
                        {
                            $new_special_ceremony_attendances[$student_fn]->set_responsibility_status(ResponsibilityStatus::AcceptedSignatures);
                        }
                        // 3) Old status was accepted for diplomas and new status is unchanged => do not invalidate the accepted status
                        else if ($old_responsibility_status === ResponsibilityStatus::AcceptedDiplomas &&
                                 $new_responsibility_status === ResponsibilityStatus::WaitingDiplomas)
                        {
                            $new_special_ceremony_attendances[$student_fn]->set_responsibility_status(ResponsibilityStatus::AcceptedDiplomas);
                        }
                        // 4) In all other cases, we the new responsibility status is already correct, no need to do anything
                    }
                }
                
                $ordinary_ceremony_attendances = $this->get_ordinary_ceremony_attendances(
                    array_values($new_special_ceremony_attendances),
                    $ceremony_info["id"],
                    $ceremony_info["graduation_year"]
                );
                $all_ceremony_attendances = [...$new_special_ceremony_attendances, ... $ordinary_ceremony_attendances];
                
                $this->ceremonies_attendance_service->update_many_ceremony_attendances($all_ceremony_attendances);
                
                // Finally, update the ceremony info
                $this->update_ceremony_info($ceremony_info);
            }
        );
    }

    public function get_all_ceremony_list_info()
    {
        $insert_query = <<<IQ
            SELECT Ceremony.id, date, Ceremony.graduation_year, student_fn, speach_status, responsibility_status FROM Ceremony
            JOIN Ceremony_Attendance ON Ceremony.id = Ceremony_Attendance.ceremony_id
            JOIN Students ON Students.fn = ceremony_attendance.student_fn
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

        $ceremony_info_all = $this->get_all_ceremonies_info_from_rows($ceremony_info_rows);

        return $ceremony_info_all;
    }

    public function get_ceremony_info_by_id($id) 
    {
        $select_query = <<<IQ
            SELECT Ceremony.id, date, Ceremony.graduation_year, student_fn, speach_status, responsibility_status FROM Ceremony
            JOIN Ceremony_Attendance ON Ceremony.id = Ceremony_Attendance.ceremony_id
            JOIN Students ON Students.fn = ceremony_attendance.student_fn
            WHERE ((speach_status != "declined" AND Ceremony_Attendance.speach_status != "none")
                OR (responsibility_status NOT LIKE '%declined%' AND responsibility_status != "none"))
                AND (Ceremony.id = ?)
            ORDER BY Ceremony.graduation_year
        IQ;

        $map_func = function ($row) {
            return $row;
        };

        $ceremony_info_rows = parent::find_all_with_query_map($select_query, [$id], $map_func);
        $ceremony_info = $this->get_all_ceremonies_info_from_rows($ceremony_info_rows)[0];

        if (!$ceremony_info) {
            return false;
        }

        return $ceremony_info;
    }

    public function get_ceremony_simple_info_by_id($id) 
    {
        $select_query = <<<IQ
            SELECT date, graduation_year, id FROM Ceremony
            WHERE id = :id
        IQ;

        $ceremony_info = parent::get_with_query_map($select_query, ["id" => $id],
            function ($row)
            {
                $row["date"] = DateTime::createFromFormat("Y-m-d H:i:s", $row["date"]);
                return new Ceremony($row["date"], $row["graduation_year"], $row["id"]);
            });

        if (!$ceremony_info) {
            return false;
        }

        return $ceremony_info;
    }

    private function get_all_ceremonies_info_from_rows($ceremony_info_rows)
    {
        $ceremony_info_all = [];

        $id_current = $ceremony_info_rows[0]["id"];
        $date_current = $ceremony_info_rows[0]["date"];
        $speaker_info_current = "none";
        $responsible_robes_info_current = "none";
        $responsible_signatures_info_current = "none";
        $responsible_diplomas_info_current = "none";
        $graduation_year_current = $ceremony_info_rows[0]["graduation_year"];

        // Add dummy last val so we can add the last ceremony info
        $ceremony_info_rows[] = false;
        foreach ($ceremony_info_rows as $row) {
            if (!$row || $id_current != $row["id"]) {
                // Store current ceremony info
                array_push($ceremony_info_all, [
                    "id" => $id_current,
                    "date" => $date_current,
                    "graduation_year" => $graduation_year_current,
                    "speaker" => $speaker_info_current,
                    "responsible_robes" => $responsible_robes_info_current,
                    "responsible_signatures" => $responsible_signatures_info_current,
                    "responsible_diplomas" => $responsible_diplomas_info_current,
                ]);

                // Reset current ceremony info
                $id_current = $row["id"];
                $date_current = $row["date"];
                $speaker_info_current = "none";
                $responsible_robes_info_current = "none";
                $responsible_signatures_info_current = "none";
                $responsible_diplomas_info_current = "none";
                $graduation_year_current = $row["graduation_year"];
            }

            if ($speaker_info_current === "none") {
                $speaker_info_new = $this->get_ceremony_special_people_string(
                    $row["student_fn"],
                    $row["speach_status"],
                    [SpeachStatus::Waiting, SpeachStatus::Accepted],
                    SpeachStatus::class
                );
                $speaker_info_current = $speaker_info_new;
            }

            if ($responsible_robes_info_current === "none") {
                $responsible_robes_info_new = $this->get_ceremony_special_people_string(
                    $row["student_fn"],
                    $row["responsibility_status"],
                    [ResponsibilityStatus::WaitingRobes, ResponsibilityStatus::AcceptedRobes],
                    ResponsibilityStatus::class
                );
                $responsible_robes_info_current = $responsible_robes_info_new;
            }
            if ($responsible_signatures_info_current === "none") {
                $responsible_signatures_info_new = $this->get_ceremony_special_people_string(
                    $row["student_fn"],
                    $row["responsibility_status"],
                    [ResponsibilityStatus::WaitingSignatures, ResponsibilityStatus::AcceptedSignatures],
                    ResponsibilityStatus::class
                );
                $responsible_signatures_info_current = $responsible_signatures_info_new;
            }
            if ($responsible_diplomas_info_current === "none") {
                $responsible_diplomas_info_new = $this->get_ceremony_special_people_string(
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

    private function get_ceremony_special_people_string(
        $check_fn, 
        $checked_value, 
        $acceptable_values, 
        $acceptable_value_type
    )
    {
        $Unconfirmed_Status = " (Unconfirmed) ";
        $Confirmed_Status = " (Confirmed) ";

        if (in_array($acceptable_value_type::tryFrom($checked_value), $acceptable_values)) {
            return $check_fn
                . (str_starts_with($checked_value, "waiting") ? $Unconfirmed_Status : $Confirmed_Status);
        }

        return "none";
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
            function ($value): string 
            {
                return $value->to_array()["student_fn"]; 
            },
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

    private function update_ceremony_info($ceremony)
    {
        $update_query = <<<IQ
            UPDATE Ceremony
            SET date = :date, graduation_year = :graduation_year
            WHERE id = :id;
        IQ;

        parent::update_with_query($update_query, $ceremony);
    }

    private function update_ceremony_entirely(
        $ceremony_info, 
        $speaker, 
        $responsible_robes, 
        $responsible_signatures, 
        $responsible_diplomas)
    {
        // Update ceremony attendances first
        $special_ceremony_attendances = $this->get_special_ceremony_attendances(
            $ceremony_info["id"],
            $speaker,
            $responsible_robes,
            $responsible_signatures,
            $responsible_diplomas
        );
        $ordinary_ceremony_attendances = $this->get_ordinary_ceremony_attendances(
            $special_ceremony_attendances,
            $ceremony_info["id"],
            $ceremony_info["graduation_year"]
        );
        $all_ceremony_attendances = [...$special_ceremony_attendances, ... $ordinary_ceremony_attendances];
        $this->ceremonies_attendance_service->update_many_ceremony_attendances($all_ceremony_attendances);
        
        // Update ceremony info second
        $this->update_ceremony_info($ceremony_info);
    }
}
?>