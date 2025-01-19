<?php
class AuthorizationMiddleware {
    public function is_authenticated($next) {
        return function() use ($next) {
            session_start();
            if (!isset($_SESSION["id"])) {
                http_response_code(401);
                echo json_encode(["Message" => "Fail: not authenticated"]);
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
                http_response_code(403);
                echo json_encode(["Message" => "Fail: access denied"]);
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