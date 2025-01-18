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


    public function register($data)
    {

        // $this->authentication_service->register($data);

        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

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

            $password_hash = password_hash($password, PASSWORD_ARGON2ID);


            $student = json_encode($this->students_service->get_student_by_fn($fn), JSON_UNESCAPED_UNICODE);
            echo $student;

            $user = [new User($email, $password_hash, $username, $role)];

            echo json_encode($this->authentication_service->insert_many($user), JSON_UNESCAPED_UNICODE);

            $response = [
                'success' => true,
                'message' => "Registration successful for $username with email $email"
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Username, email and password are required'
            ];
        }

        echo json_encode($response);

    }
}
?>