<?php
class AuthorizationMiddleware {
    public function is_authenticated($next) {
        return function() use ($next) {
            session_start();
            if (!isset($_SESSION["id"])) {
                $base_url = $_ENV["BASE_URL"];
                http_response_code(401);
                echo json_encode(["Message" => "Fail: not authenticated"]);
                header("Location: $base_url/login");
                return;
            }
    
            $next();
        };
    }

    public function is_authorized($role, $next) {
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
        });
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