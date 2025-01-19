<?php
require_once __DIR__ . "/../services/authentication_service.php";
require_once __DIR__ . "/../services/students_service.php";
require_once __DIR__ . "/../services/clothes_service.php";

class UserController
{
    private AuthenticationService $authentication_service;
    private StudentsService $students_service;
    private ClothesService $clothes_service;


    function __construct($authentication_service, $students_service, $clothes_service)
    {
        $this->authentication_service = $authentication_service;
        $this->students_service = $students_service;
        $this->clothes_service = $clothes_service;
    }

    public function show_profile_page()
    {
        $controller = $this;
        require_once __DIR__ . "/../pages/profile/index.php";
    }


    public function get_user()
    {
        session_start();
        $id = $_SESSION["id"];
        return $this->authentication_service->get_user_by_id($id);
    }
}
?>