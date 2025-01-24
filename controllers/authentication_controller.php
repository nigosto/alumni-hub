<?php
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../services/requests_service.php";

class AuthenticationController
{
    private $users_service;
    private $students_service;
    private RequestsService $requests_service;

    function __construct($users_service, $students_service, $requests_service)
    {
        $this->users_service = $users_service;
        $this->students_service = $students_service;
        $this->requests_service = $requests_service;

    }
    public function show_register_page()
    {
        require_once __DIR__ . "/../pages/register/index.php";
    }

    public function show_login_page()
    {
        require_once __DIR__ . "/../pages/login/index.php";
    }

    public function show_pick_fn_page()
    {
        $controller = $this;
        require_once __DIR__ . "/../pages/login/pick-fn/index.php";
    }

    private function assign_fn_for_user($fn, $user_id)
    {
        if (!$fn || !$user_id) {
            throw new Exception("Missing faculty number or user_id");
        }

        $student = $this->students_service->get_student_by_fn($fn);
        if (!$student) {
            throw new Exception('Incorrect faculty number!');
        }
        $this->students_service->update_user_id($fn, $user_id);

        session_start();
        $_SESSION["fn"] = $fn;
    }

    public function add_fn($data)
    {
        if (!isset($data["fn"])) {
            throw new Exception("Missing fn");
        }

        session_start();
        $user_id = $_SESSION["id"];
        $request = new Request($user_id, $data["fn"]);
        $this->requests_service->insert_request($request);

        // $this->assign_fn_for_user($data["fn"], $user_id);
    }
    public function register($data)
    {
        if (isset($data['username']) && isset($data['email']) && isset($data['password']) && isset($data['role']) && isset($data["password_confirmation"])) {
            $username = $data['username'];
            $email = $data['email'];
            $role = strtolower($data['role']);
            $password = $data['password'];
            $password_confirmation = $data['password_confirmation'];
            $fn = $data['fn'];

            if ($password !== $password_confirmation) {
                throw new Exception('Mismatch in passwords!');
            }

            if (($role === 'student' && ($fn === null || $fn === "")) || ($role === 'administrator' && ($fn !== null && $fn !== ''))) {
                throw new Exception('Invalid faculty number!');
            }

            $role = Role::tryFrom($role);

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            session_start();

            if ($role === Role::Student) {
                $user = new User(null, $email, $password_hash, $username, $role->value, true);
                
                $registered_user_id = $this->users_service->insert($user);
                
                $request = new Request($registered_user_id, $fn);
                $this->requests_service->insert_request($request);

                // $this->assign_fn_for_user($fn, $registered_user_id);
            } else {
                $user = new User(null, $email, $password_hash, $username, $role->value, false);
                $registered_user_id = $this->users_service->insert($user);
                
                $_SESSION["role"] = $role;
                $_SESSION["id"] = $registered_user_id;
            }

        } else {
            throw new Exception(
                'Username, email and password are required'
            );
        }
    }

    public function login($data)
    {
        if (isset($data['username']) && isset($data['password'])) {
            $username = $data['username'];
            $password = $data['password'];

            $user = $this->users_service->get_user_by_username($username);

            if (!$user->compare_password($password)) {
                throw new Exception('Wrong password or username!');
            }

            session_start();
            $user_data = $user->to_array();
            $_SESSION["role"] = Role::tryFrom($user_data["role"]);
            $_SESSION["id"] = $user->get_id();
            return $user;
        } else {
            throw new Exception(
                'Username and password are required'
            );
        }
    }

    public function set_fn($data)
    {
        if (isset($data['fn'])) {
            $fn = $data['fn'];
            session_start();
            $_SESSION["fn"] = $fn;
        } else {
            throw new Exception(
                'Faculty number is empty!'
            );
        }
    }
}
?>