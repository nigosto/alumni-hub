<?php
require_once __DIR__ . "/../services/ceremonies_service.php";
require_once __DIR__ . "/../services/ceremonies_attendance_service.php";
require_once __DIR__ . "/../services/ceremony_students_export_service.php";
require_once __DIR__ . "/../services/students_service.php";

class CeremoniesController
{
    private CeremoniesService $ceremonies_service;
    private CeremoniesAttendanceService $ceremonies_attendance_service;
    private CeremonyStudentsExportService $ceremony_students_export_service;
    private StudentsService $students_service;

    function __construct(
        CeremoniesService $ceremonies_service, 
        CeremoniesAttendanceService $ceremonies_attendance_service,
        CeremonyStudentsExportService $ceremony_students_export_service,
        StudentsService $students_service)
    {
        $this->ceremonies_service = $ceremonies_service;
        $this->ceremonies_attendance_service = $ceremonies_attendance_service;
        $this->ceremony_students_export_service = $ceremony_students_export_service;
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

    public function show_ceremonies_edit_page($ceremony_id)
    {
        $ceremonies_controller = $this;
        require_once __DIR__ . "/../pages/ceremonies/edit_ceremony/index.php";
    }

    public function show_ceremonies_students_page($ceremony_id)
    {
        $ceremony_info = $this->get_ceremony_simple_info_by_id($ceremony_id);
        if (!$ceremony_info)
        {
            require_once __DIR__ . "/../pages/not_found/index.php";
        }
        else 
        {
            $ceremonies_controller = $this;
            require_once __DIR__ . "/../pages/ceremonies/students_list/index.php";
        }
    }

    public function export_students($ceremony_id)
    {
        $students = $this->ceremonies_service->get_ceremony_students_info($ceremony_id);
        $this->ceremony_students_export_service->export($students);
    }

    public function get_ceremony_students_info($ceremony_id)
    {
        $ceremoniy_students_info = $this->ceremonies_service->get_ceremony_students_info($ceremony_id);

        if (!$ceremoniy_students_info)
        {
            return [];
        }

        return $ceremoniy_students_info;
    }

    private function validate_create_or_update_ceremony_data($date, 
        $graduation_year, 
        $speaker,
        $responsible_robes,
        $responsible_signatures,
        $responsible_diplomas)
    {
        // TODO: Validation for: 
        // 1) requested students being in the same graduation year as the ceremony
        // 2) already created ceremony for graduation year
        if (!$date) 
        {
            throw new Exception('Невалидна дата!');
        }

        if ($date->format('Y') < $graduation_year) 
        {
            throw new Exception('Датата за церемонията трябва да е след датата на завършването!');
        }

        if ($responsible_robes === $responsible_diplomas ||
            $responsible_robes === $responsible_signatures ||
            $responsible_signatures === $responsible_diplomas) 
        {
            throw new Exception('Един студент не може да има повече от една отговорност!');
        }

        if (
            !$this->students_service->get_student_by_fn($responsible_diplomas) || 
            !$this->students_service->get_student_by_fn($responsible_robes) || 
            !$this->students_service->get_student_by_fn($responsible_signatures) ||
            !$this->students_service->get_student_by_fn($speaker)
        ) {
            throw new Exception('Моля въведете валидни факултетни номера!');
        }
    }

    public function update_speach_status($ceremony_id, $student_fn, SpeachStatus $status) {
        return $this->ceremonies_attendance_service->update_speach_status($ceremony_id, $student_fn, $status);
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
            $date = $this->ceremonies_service->create_date_from_js_string($data['date']);
            $graduation_year = $data['graduation_year'];
            $speaker = $data['speaker'];
            $responsible_robes = $data['responsible_robes'];
            $responsible_signatures = $data['responsible_signatures'];
            $responsible_diplomas = $data['responsible_diplomas'];

            $this->validate_create_or_update_ceremony_data($date, 
                $graduation_year, 
                $speaker,
                $responsible_robes, 
                $responsible_signatures, 
                $responsible_diplomas);

            $ceremony = new Ceremony($date, $graduation_year);
            $this->ceremonies_service->insert_ceremony(
                $ceremony,
                $speaker,
                $responsible_robes,
                $responsible_signatures,
                $responsible_diplomas
            );
        }
    }

    public function get_ceremonies_list_data()
    {
        $ceremonies_info = $this->ceremonies_service->get_all_ceremony_list_info();
        if (!$ceremonies_info)
        {
            return [];
        }

        return $ceremonies_info;
    }

    public function get_ceremony_by_id($ceremony_id)
    {
        $ceremony_info = $this->ceremonies_service->get_ceremony_info_by_id($ceremony_id);
        if (!$ceremony_info)
        {
            return null;
        }

        return $ceremony_info;
    }

    public function get_ceremony_simple_info_by_id($ceremony_id)
    {
        $ceremony_info = $this->ceremonies_service->get_ceremony_simple_info_by_id($ceremony_id);
        if (!$ceremony_info)
        {
            return null;
        }

        return $ceremony_info;
    }

    public function update_ceremony($data)
    {
        if (isset($data["id"]) &&
            isset($data['date']) && 
            isset($data['graduation_year']) && 
            isset($data['speaker']) && 
            isset($data['responsible_robes']) && 
            isset($data["responsible_signatures"]) && 
            isset($data["responsible_diplomas"])) 
        {
            $id = $data["id"];
            $date = $this->ceremonies_service->create_date_from_js_string($data['date']);
            $graduation_year = $data['graduation_year'];
            $speaker = $data['speaker'];
            $responsible_robes = $data['responsible_robes'];
            $responsible_signatures = $data['responsible_signatures'];
            $responsible_diplomas = $data['responsible_diplomas'];

            $this->validate_create_or_update_ceremony_data($date, 
                $graduation_year, 
                $speaker,
                $responsible_robes, 
                $responsible_signatures, 
                $responsible_diplomas);

            $ceremony = new Ceremony($date, $graduation_year, $id);
            $this->ceremonies_service->update_ceremony(
                $ceremony,
                $speaker,
                $responsible_robes,
                $responsible_signatures,
                $responsible_diplomas
            );
        }
    }

    public function update_ceremony_invitation($data) {
        $ceremony_id = intval($data["ceremony_id"]);
        $status = boolval($data["status"]);

        session_start();
        $student_fn = $_SESSION["fn"];

        $this->ceremonies_attendance_service->update_accepted_status($ceremony_id, $student_fn, $status);

        if ($status === false) {
            $attendance = $this->ceremonies_attendance_service->find_one_for_student($ceremony_id, $student_fn);
            $attendance = $attendance->to_array();
            $responsibility_status = ResponsibilityStatus::tryFrom($attendance["responsibility_status"]);
            $speach_status = SpeachStatus::tryFrom($attendance["speach_status"]);

            if ($speach_status !== SpeachStatus::None) {
                $this->ceremonies_attendance_service->update_speach_status($ceremony_id, $student_fn, SpeachStatus::Declined);
            }
            
            if ($responsibility_status === ResponsibilityStatus::AcceptedDiplomas || $responsibility_status === ResponsibilityStatus::WaitingDiplomas) {
                $this->ceremonies_attendance_service->update_responsibility_status($ceremony_id, $student_fn, ResponsibilityStatus::DeclinedDiplomas);
            } else if ($responsibility_status === ResponsibilityStatus::AcceptedRobes || $responsibility_status === ResponsibilityStatus::WaitingRobes) {
                $this->ceremonies_attendance_service->update_responsibility_status($ceremony_id, $student_fn, ResponsibilityStatus::DeclinedRobes);
            } else if ($responsibility_status === ResponsibilityStatus::AcceptedSignatures || $responsibility_status === ResponsibilityStatus::WaitingSignatures) {
                $this->ceremonies_attendance_service->update_responsibility_status($ceremony_id, $student_fn, ResponsibilityStatus::DeclinedSignatures);
            }
        }
    }
}
?>