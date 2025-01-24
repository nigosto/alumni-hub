<?php
require_once __DIR__ . "/../services/users_service.php";

class AuthorizationMiddleware {
    private UsersService $user_service;

    function __construct($user_service) {
        $this->user_service = $user_service;
    }

    public function is_authenticated($next, $check_approval = true) {
        return function() use ($next, $check_approval) {
            session_start();
            if (!isset($_SESSION["id"])) {
                $base_url = $_ENV["BASE_URL"];
                http_response_code(401);
                echo json_encode(["Message" => "Fail: not authenticated"]);
                header("Location: $base_url/login");
                return;
            }
    
            $user = $this->user_service->get_user_by_id($_SESSION["id"]);

            if ($check_approval && !$user->to_array()["approved"]) {
                $base_url = $_ENV["BASE_URL"];
                header("Location: $base_url/not-approved");
                return;
            }

            $next();
        };
    }

    public function is_authorized($role, $next, $check_approval = true) {
        return $this->is_authenticated(function() use ($role, $next) {
            session_start();
            $session_role = $_SESSION["role"];
            if (!isset($session_role) || $session_role !== $role) {
                $base_url = $_ENV["BASE_URL"];
                http_response_code(403);
                echo json_encode(["Message" => "Fail: access denied"]);
                header("Location: $base_url/access-denied");
                return;
            }

            $next();
        }, $check_approval);
    }

    public function is_not_authenticated($next) {
        return function() use ($next) {
            session_start();
            if (isset($_SESSION["id"])) {
                http_response_code(403);
                echo json_encode(["Message" => "Fail: access denied"]);
                return;
            }
    
            $next();
        };
    }
}
?>