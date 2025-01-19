<?php
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../models/user.php";

class AdminController {
    private UsersService $users_service;

    function __construct(UsersService $users_service) {
        $this->users_service = $users_service;
    }

    public function show_approval_page() {
        $controller = $this;
        require_once __DIR__ . "/../pages/admin/index.php";      
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
}
?>