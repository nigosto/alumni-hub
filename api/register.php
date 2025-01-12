<?php
require __DIR__ . "/../services/authentication/index.php";
require __DIR__ . "/../services/students/index.php";

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

    if (($role === 'student' && $fn === null) || ($role === 'administrator' && $fn !== null)) {
        throw new Exception('Invalid faculty number!');
    }

    $password_hash = password_hash($password, PASSWORD_ARGON2ID);

    // TODO validate that there exists a student with this fn, 
    // then after creation of user, populate the userId in students
    // $student = json_encode($students_service->get_student_by_fn($fn), JSON_UNESCAPED_UNICODE);
    // echo $student;

    $user = [new User($email, $password_hash, $username, $role)];

    echo json_encode($authentication_service->insert_many($user), JSON_UNESCAPED_UNICODE);

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
?>