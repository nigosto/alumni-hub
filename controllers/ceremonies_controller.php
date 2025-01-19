<?php
class CeremoniesController
{
    private $ceremonies_service;
    private $ceremonies_attendance_service;
    private $students_service;

    function __construct($ceremonies_service, $ceremonies_attendance_service, $students_service)
    {
        $this->ceremonies_service = $ceremonies_service;
        $this->ceremonies_attendance_service = $ceremonies_attendance_service;
        $this->students_service = $students_service;
    }

    public function show_create_ceremony_page()
    {
        require_once __DIR__ . "/../pages/ceremonies/create_ceremony/index.php";
    }

    public function show_ceremonies_list_page()
    {
        $controller = $this;
        require_once __DIR__ . "/../pages/ceremonies/ceremonies_list/index.php";
    }

    private function validate_create_ceremony_data($date, 
        $graduation_year, 
        $responsible_robes,
        $responsible_signatures,
        $responsible_diplomas)
    {
        // TODO: Validation for: 
        // 1) requested students being in the same graduation year as the ceremony
        // 2) already created ceremony for graduation year
        if (!$date) 
        {
            throw new Exception('Invalid date!');
        }

        if ($date->format('Y') < $graduation_year) 
        {
            throw new Exception('Ceremony date is before graduation year');
        }

        if ($responsible_robes === $responsible_diplomas ||
            $responsible_robes === $responsible_signatures ||
            $responsible_signatures === $responsible_diplomas) 
        {
            throw new Exception('The same student cannot be assigned more than one responsibility');
        }
    }

    private function get_special_ceremony_attendances(
        $ceremony_id,
        $speaker,
        $responsible_robes,
        $responsible_signatures,
        $responsible_diplomas)
    {
        // The speaking student can also have a responsibility
        $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::None;
        if ($responsible_robes === $speaker) {
            $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingRobes;
        }
        else if ($responsible_signatures === $speaker) {
            $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingSignatures;
        }
        else if ($responsible_diplomas === $speaker) {
            $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingDiplomas;
        }

        $ceremony_attendances = [];
        $ceremony_attendance_speaker = new CeremonyAttendance($ceremony_id, $speaker, null, 
                SpeachStatus::Waiting, $ceremony_attendance_speaker_responsibility);
        array_push($ceremony_attendances, $ceremony_attendance_speaker);

        if ($ceremony_attendance_speaker_responsibility !== ResponsibilityStatus::WaitingRobes) 
        {
            $ceremony_attendance_responsible_robes = new CeremonyAttendance(
                $ceremony_id, $responsible_robes, null, 
                SpeachStatus::None, ResponsibilityStatus::WaitingRobes);
            array_push($ceremony_attendances, $ceremony_attendance_responsible_robes);
        }
        if ($ceremony_attendance_speaker_responsibility !== ResponsibilityStatus::WaitingSignatures) 
        {
            $ceremony_attendance_responsible_signatures = new CeremonyAttendance(
                $ceremony_id, $responsible_signatures, null, 
                SpeachStatus::None, ResponsibilityStatus::WaitingSignatures);
            array_push($ceremony_attendances, $ceremony_attendance_responsible_signatures);
        }
        if ($ceremony_attendance_speaker_responsibility !== ResponsibilityStatus::WaitingDiplomas) 
        {
            $ceremony_attendance_responsible_diplomas = new CeremonyAttendance(
                $ceremony_id, $responsible_diplomas, null, 
                SpeachStatus::None, ResponsibilityStatus::WaitingDiplomas);
            array_push($ceremony_attendances, $ceremony_attendance_responsible_diplomas);
        }

        return $ceremony_attendances;
    }

    private function get_ordinary_ceremony_attendances($special_ceremony_attendances, $ceremony_id, $graduation_year)
    {
        $special_students_fns = array_map(
            function($value): string { return $value->to_array()["student_fn"]; }, 
        $special_ceremony_attendances);
        $ordinary_students_fns = $this->students_service->get_ordinary_students_fns_for_graduation_year($graduation_year, $special_students_fns);
        
        $ordinary_ceremony_attendances = [];
        foreach ($ordinary_students_fns as $ordinary_student_fn)
        {
            $ordinary_ceremony_attendance = new CeremonyAttendance(
                $ceremony_id, $ordinary_student_fn, null, 
                SpeachStatus::None, ResponsibilityStatus::None);
            array_push($ordinary_ceremony_attendances, $ordinary_ceremony_attendance);
        }

        return $ordinary_ceremony_attendances;
    }

    public function create_ceremony($data)
    {
        if (isset($data['date']) && 
            isset($data['graduation_year']) && 
            isset($data['speaker']) && 
            isset($data['responsible_robes']) && 
            isset($data["responsible_signatures"]) && 
            isset($data["responsible_diplomas"])) 
        {

            $date = $this->ceremonies_service->create_date_from_string($data['date']);
            $graduation_year = $data['graduation_year'];
            $speaker = $data['speaker'];
            $responsible_robes = $data['responsible_robes'];
            $responsible_signatures = $data['responsible_signatures'];
            $responsible_diplomas = $data['responsible_diplomas'];

            $this->validate_create_ceremony_data($date, 
                $graduation_year, 
                $responsible_robes, 
                $responsible_signatures, 
                $responsible_diplomas);

            $ceremony = new Ceremony($date, $graduation_year);
            $ceremony_id = $this->ceremonies_service->insert_ceremony($ceremony);

            $special_ceremony_attendances = $this->get_special_ceremony_attendances(
                $ceremony_id, 
                $speaker, 
                $responsible_robes, 
                $responsible_signatures, 
                $responsible_diplomas);

            $ordinary_ceremony_attendances = $this->get_ordinary_ceremony_attendances(
                $special_ceremony_attendances, 
                $ceremony_id, 
                $graduation_year);

            $ceremony_attendances = array_merge($special_ceremony_attendances, $ordinary_ceremony_attendances);
            $this->ceremonies_attendance_service->insert_many_ceremony_attendances($ceremony_attendances);
        }
    }

    public function get_ceremonies_data()
    {
        $ceremonies_info = $this->ceremonies_service->get_all_ceremony_info();
        if (!$ceremonies_info)
        {
            return [];
        }

        return $ceremonies_info;
    }
}
?>