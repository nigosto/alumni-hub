<?php
require_once __DIR__ . "/../services/ceremonies_attendance_service.php";

class CeremoniesController
{
    private $ceremonies_service;
    private CeremoniesAttendanceService $ceremonies_attendance_service;

    function __construct($ceremonies_service, CeremoniesAttendanceService $ceremonies_attendance_service)
    {
        $this->ceremonies_service = $ceremonies_service;
        $this->ceremonies_attendance_service = $ceremonies_attendance_service;
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

    public function update_speach_status($ceremony_id, $student_fn, SpeachStatus $status) {
        return $this->ceremonies_attendance_service->update_speach_status($ceremony_id, $student_fn, $status);
    }

    public function update_accepted_status($ceremony_id, $student_fn, $status) {
        return $this->ceremonies_attendance_service->update_accepted_status($ceremony_id, $student_fn, $status);
    }

    public function update_responsibility_status($ceremony_id, $student_fn, ResponsibilityStatus $status) {
        return $this->ceremonies_attendance_service->update_responsibility_status($ceremony_id, $student_fn, $status);
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
            $this->ceremonies_service->insert_ceremony(
                $ceremony,
                $graduation_year,
                $speaker,
                $responsible_robes,
                $responsible_signatures,
                $responsible_diplomas
            );
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