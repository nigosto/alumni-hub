<?php
class CeremoniesController
{
    private $ceremonies_service;
    private $ceremonies_attendance_service;

    function __construct($ceremonies_service, $ceremonies_attendance_service)
    {
        $this->ceremonies_service = $ceremonies_service;
        $this->ceremonies_attendance_service = $ceremonies_attendance_service;
    }

    public function get_ceremony_by_id()
    {
        return $this->ceremonies_service->get_ceremony_by_id(1);
    }

    public function show_ceremonies_page()
    {
        require_once __DIR__ . "/../pages/ceremonies/index.php";
    }
}
?>