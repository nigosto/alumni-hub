<?php
class AuthenticationController
{
    private $authentication_service;
    private $students_service;

    function __construct($authentication_service, $students_service)
    {
        $this->authentication_service = $authentication_service;
        $this->students_service = $students_service;

    }
    public function show_register_page()
    {
        require_once __DIR__ . "/../pages/register/index.php";
    }


    public function show_login_page()
    {
        require_once __DIR__ . "/../pages/login/index.php";
    }

    public function register($data)
    {
        if (isset($data['username']) && isset($data['email']) && isset($data['password']) && isset($data['role']) && isset($data["password_confirmation"])) {
            $username = $data['username'];
            $email = $data['email'];
            $role = $data['role'];
            $password = $data['password'];
            $password_confirmation = $data['password_confirmation'];
            $fn = $data['fn'];

            if ($password !== $password_confirmation) {
                throw new Exception('Mismatch in passwords!');
            }

            if (($role === 'student' && ($fn === null || $fn === "")) || ($role === 'administrator' && ($fn !== null && $fn !== ''))) {
                throw new Exception('Invalid faculty number!');
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $user = new User(null, $email, $password_hash, $username, $role);

            if ($role === 'student') {
                $student = $this->students_service->get_student_by_fn($fn);
                if (!$student) {
                    throw new Exception('Incorrect faculty number!');
                }

                $registered_user_id = $this->authentication_service->insert($user);
                $this->students_service->update_user_id($fn, $registered_user_id);
            } else {
                $this->authentication_service->insert($user);
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

            $user = $this->authentication_service->get_user($username);
            if (!$user->compare_password($password)) {
                throw new Exception('Wrong password or username!');
            }

            session_start();

            $_SESSION["role"] = $user->get_role();
            $_SESSION["id"] = $user->get_id();

        } else {
            throw new Exception(
                'Username and password are required'
            );
        }
    }
}
?>