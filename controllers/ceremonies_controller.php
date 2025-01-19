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

    public function get_ceremony_by_id()
    {
        return $this->ceremonies_service->get_ceremony_by_id(1);
    }

    public function show_create_ceremony_page()
    {
        require_once __DIR__ . "/../pages/ceremonies/create_ceremony/index.php";
    }

    public function create_ceremony($data)
    {
        # TODO: Authentication

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

            $ceremony = new Ceremony($date);
            $ceremony_id = $this->ceremonies_service->insert_ceremony($ceremony);

            $SpeakerResponsibility = new class {
                public const None = 0;
                public const Robes = 1;
                public const Signatures = 2;
                public const Diplomas = 3;
            };

            // The speaking student can also have a responsibility
            $speaker_is_responsible = $SpeakerResponsibility::None;
            $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::None;
            if ($responsible_robes === $speaker) {
                $speaker_is_responsible = $SpeakerResponsibility::Robes;
                $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingRobes;
            }
            else if ($responsible_signatures === $speaker) {
                $speaker_is_responsible = $SpeakerResponsibility::Signatures;
                $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingSignatures;
            }
            else if ($responsible_diplomas === $speaker) {
                $speaker_is_responsible = $SpeakerResponsibility::Diplomas;
                $ceremony_attendance_speaker_responsibility = ResponsibilityStatus::WaitingDiplomas;
            }

            $ceremony_attendances = [];

            $ceremony_attendance_speaker = new CeremonyAttendance($ceremony_id, $speaker, null, 
                SpeachStatus::Waiting, $ceremony_attendance_speaker_responsibility);
            array_push($ceremony_attendances, $ceremony_attendance_speaker);

            if ($speaker_is_responsible !== $SpeakerResponsibility::Robes) 
            {
                $ceremony_attendance_responsible_robes = new CeremonyAttendance(
                    $ceremony_id, $responsible_robes, null, 
                    SpeachStatus::None, ResponsibilityStatus::WaitingRobes);
                array_push($ceremony_attendances, $ceremony_attendance_responsible_robes);
            }
            if ($speaker_is_responsible !== $SpeakerResponsibility::Signatures) 
            {
                $ceremony_attendance_responsible_signatures = new CeremonyAttendance(
                    $ceremony_id, $responsible_signatures, null, 
                    SpeachStatus::None, ResponsibilityStatus::WaitingSignatures);
                array_push($ceremony_attendances, $ceremony_attendance_responsible_signatures);
            }
            if ($speaker_is_responsible !== $SpeakerResponsibility::Diplomas) 
            {
                $ceremony_attendance_responsible_diplomas = new CeremonyAttendance(
                    $ceremony_id, $responsible_diplomas, null, 
                    SpeachStatus::None, ResponsibilityStatus::WaitingDiplomas);
                array_push($ceremony_attendances, $ceremony_attendance_responsible_diplomas);
            }

            $special_students_fns = array_map(
                function($value): string { return $value->to_array()["student_fn"]; }, 
            $ceremony_attendances);
            $ordinary_students_fns = $this->students_service->get_ordinary_students_fns_for_graduation_year($graduation_year, $special_students_fns);
            
            foreach ($ordinary_students_fns as $ordinary_student_fn)
            {
                $ordinary_ceremony_attendance = new CeremonyAttendance(
                    $ceremony_id, $ordinary_student_fn, null, 
                    SpeachStatus::None, ResponsibilityStatus::None);
                array_push($ceremony_attendances, $ordinary_ceremony_attendance);
            }

            var_dump($ceremony_attendances);
            $this->ceremonies_attendance_service->insert_many_ceremony_attendances($ceremony_attendances);
        }
    }
}
?>