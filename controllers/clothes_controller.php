<?php
class ClothesController
{
    private $clothes_service;
    function __construct($clothes_service)
    {
        $this->clothes_service = $clothes_service;
    }

    public function assign_clothing($data)
    {
        if (isset($data['size'])) {
            session_start();
            $fn = $_SESSION["fn"];
            $size = $data['size'];
            $this->clothes_service->assign_clothing($size, $fn);
        } else {
            throw new Exception(
                'Невалиден размер!'
            );
        }
    }
}
?>