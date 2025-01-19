<?php
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../services/students_service.php";

class UserController
{
    private UsersService $users_service;
    private StudentsService $students_service;


    function __construct($users_service, $students_service)
    {
        $this->users_service = $users_service;
        $this->students_service = $students_service;
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
        return $this->users_service->get_user_by_id($id);
    }
}
?>