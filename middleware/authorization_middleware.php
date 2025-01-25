<?php
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../models/user.php";

class AuthorizationMiddleware {
    private UsersService $user_service;

    function __construct($user_service) 
    {
        $this->user_service = $user_service;
    }

    public function is_authenticated($next, $check_approval = true) 
    {
        return function($params) use ($next, $check_approval) {
            session_start();
            if (!isset($_SESSION["id"])) {
                $base_url = $_ENV["BASE_URL"];
                http_response_code(401);
                echo json_encode(["message" => "Неудостоверен потребител!"], JSON_UNESCAPED_UNICODE);
                header("Location: $base_url/login");
                return;
            }
    
            $user = $this->user_service->get_user_by_id($_SESSION["id"]);
            $user = $user->to_array();
            $role = Role::tryFrom($user["role"]);
            $base_url = $_ENV["BASE_URL"];

            if ($check_approval && $role !== Role::Student && !$user["approved"]) {
                header("Location: $base_url/not-approved");
                return;
            }

            if ($check_approval && $role === Role::Student && !isset($_SESSION["fn"])) {
                http_response_code(401);
                echo json_encode(["message" => "Неудостоверен студент!"], JSON_UNESCAPED_UNICODE);
                header("Location: $base_url/not-approved");
                return;
            }

            $next($params);
        };
    }

    public function is_authorized($role, $next, $check_approval = true) 
    {
        return $this->is_authenticated(function ($params) use ($role, $next) {
            session_start();
            $session_role = $_SESSION["role"];
            if (!isset($session_role) || $session_role !== $role) {
                $base_url = $_ENV["BASE_URL"];
                http_response_code(403);
                echo json_encode(["message" => "Достъпът е отказан!"], JSON_UNESCAPED_UNICODE);
                header("Location: $base_url/access-denied");
                return;
            }

            $next($params);
        }, $check_approval);
    }

    public function is_not_authenticated($next) {
        return function($params) use ($next) {
            session_start();
            if (isset($_SESSION["id"])) {
                $base_url = $_ENV["BASE_URL"];
                http_response_code(403);
                echo json_encode(["message" => "Достъпът е отказан!"], JSON_UNESCAPED_UNICODE);
                header("Location: $base_url/");
                return;
            }
            $next($params);
        };
    }
}
?>