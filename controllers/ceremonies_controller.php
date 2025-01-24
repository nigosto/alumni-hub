<?php
class CeremoniesController
{
    private $ceremonies_service;

    function __construct($ceremonies_service)
    {
        $this->ceremonies_service = $ceremonies_service;
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

    public function show_ceremonies_studdents_page($ceremony_id)
    {
        $ceremonies_controller = $this;
        require_once __DIR__ . "/../pages/ceremonies/students_list/index.php";
    }

    public function get_ceremony_students_info($ceremonies_id)
    {
        $ceremoniy_students_info = $this->ceremonies_service->get_ceremony_students_info($ceremonies_id);

        if (!$ceremoniy_students_info)
        {
            return [];
        }

        return $ceremoniy_students_info;
    }

    private function validate_create_or_update_ceremony_data($date, 
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
}
?>