<?php
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../services/requests_service.php";
require_once __DIR__ . "/../services/students_service.php";
require_once __DIR__ . "/../models/user.php";

class AdminController {
    private UsersService $users_service;
    private RequestsService $requests_service;
    private StudentsService $students_service;

    function __construct(UsersService $users_service, RequestsService $requests_service, StudentsService $students_service) {
        $this->users_service = $users_service;
        $this->requests_service = $requests_service;
        $this->students_service = $students_service;
    }

    public function show_administrator_approval_page() {
        $controller = $this;
        require_once __DIR__ . "/../pages/approval/administrators/index.php";      
    }

    public function show_students_approval_page() {
        $controller = $this;
        require_once __DIR__ . "/../pages/approval/students/index.php";      
    }

    public function get_users_data_by_role(Role $role)
    {
        return array_map(function ($user) {
            return array_values($user->to_array(true));
        }, $this->users_service->find_users_by_role($role));
    }

    public function approve_administrator($email) {
        $this->users_service->approve_user_by_email($email);
    }

    public function get_requests_data() {
        return array_values($this->requests_service->get_requests_data());
    }

    public function approve_student($username, $student_fn) {
        $user = $this->users_service->get_user_by_username($username);
        $user_id = $user->get_id();
        $this->requests_service->delete_request($user_id, $student_fn);
        $this->students_service->update_user_id($student_fn, $user_id);
    }
}
?>