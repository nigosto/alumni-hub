<?php
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../services/students_service.php";
require_once __DIR__ . "/../services/clothes_service.php";
require_once __DIR__ . "/../services/ceremonies_attendance_service.php";

class UserController
{
    private UsersService $users_service;
    private StudentsService $students_service;
    private ClothesService $clothes_service;
    private CeremoniesAttendanceService $ceremonies_attendance_service;


    function __construct($users_service, $students_service, $clothes_service, $ceremonies_attendance_service)
    {
        $this->users_service = $users_service;
        $this->students_service = $students_service;
        $this->clothes_service = $clothes_service;
        $this->ceremonies_attendance_service = $ceremonies_attendance_service;
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

    public function get_ceremonies_invitations_for_student() {
        session_start();

        if (!isset($_SESSION["fn"])) {
            throw new Exception("User is not a student");
        }

        return $this->ceremonies_attendance_service->find_by_student_fn($_SESSION["fn"]);
    }
}
?>