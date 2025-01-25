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

    public function add_fn($data)
    {
        if (!isset($data["fn"])) {
            throw new Exception("Липсващ факултетен номер!");
        }

        session_start();
        $user_id = $_SESSION["id"];
        $request = new Request($user_id, $data["fn"]);
        $this->requests_service->insert_request($request);

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
                throw new Exception('Разлика в паролите!');
            }

            if (($role === 'student' && ($fn === null || $fn === "")) || ($role === 'administrator' && ($fn !== null && $fn !== ''))) {
                throw new Exception('Невалиден факултетен номер!');
            }

            $role = Role::tryFrom($role);

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            if ($this->users_service->get_user_by_username($username)) {
                throw new Exception("Вече съществува потребител с това име");
            }

            if ($this->users_service->get_user_by_email($email)) {
                throw new Exception("Вече съществува потребител с този имейл");
            }

            session_start();

            if ($role === Role::Student) {
                $user = new User(null, $email, $password_hash, $username, $role->value, true);
                
                $registered_user_id = $this->users_service->insert($user);
                
                $request = new Request($registered_user_id, $fn);
                $this->requests_service->insert_request($request);
            } else {
                $user = new User(null, $email, $password_hash, $username, $role->value, false);
                $registered_user_id = $this->users_service->insert($user);
                
                $_SESSION["role"] = $role;
                $_SESSION["id"] = $registered_user_id;
            }

        } else {
            throw new Exception(
                'Потребителското име, имейлът и паролата са задължителни'
            );
        }
    }

    public function login($data)
    {
        if (isset($data['username']) && isset($data['password'])) {
            $username = $data['username'];
            $password = $data['password'];

            $user = $this->users_service->get_user_by_username($username);

            if (!$user || !$user->compare_password($password)) {
                throw new Exception('Грешна парола или потребителско име!');
            }

            session_start();
            $user_data = $user->to_array();
            $_SESSION["role"] = Role::tryFrom($user_data["role"]);
            $_SESSION["id"] = $user->get_id();
            return $user;
        } else {
            throw new Exception(
                'Потребителското име и паролата са задължителни'
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
                'Липсващ факултетен номер!'
            );
        }
    }
}
?>