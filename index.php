<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/utils/query_params.php";
require_once __DIR__ . "/router.php";
require_once __DIR__ . "/controllers/pages_controller.php";
require_once __DIR__ . "/controllers/students_controller.php";
require_once __DIR__ . "/controllers/ceremonies_controller.php";
require_once __DIR__ . "/controllers/authentication_controller.php";
require_once __DIR__ . "/controllers/admin_controller.php";
require_once __DIR__ . "/controllers/clothes_controller.php";
require_once __DIR__ . "/controllers/user_controller.php";
require_once __DIR__ . "/database/database.php";
require_once __DIR__ . "/services/students_service.php";
require_once __DIR__ . "/services/ceremonies_service.php";
require_once __DIR__ . "/services/ceremonies_attendance_service.php";
require_once __DIR__ . "/services/ceremony_students_export_service.php";
require_once __DIR__ . "/services/students_import_service.php";
require_once __DIR__ . "/services/students_export_service.php";
require_once __DIR__ . "/services/users_service.php";
require_once __DIR__ . "/services/clothes_service.php";
require_once __DIR__ . "/services/requests_service.php";
require_once __DIR__ . "/middleware/authorization_middleware.php";
require_once __DIR__ . "/models/user.php";

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
load_config(".env");

$router = new Router();
$database = new Database();

$students_service = new StudentsService($database);
$users_service = new UsersService($database);
$students_import_service = new StudentsImportService();
$ceremony_students_export_service = new CeremonyStudentsExportService();
$ceremonies_attendance_service = new CeremoniesAttendanceService($database, $students_service);
$ceremonies_service = new CeremoniesService(
    $database, 
    $ceremonies_attendance_service, 
    $students_service);
$students_export_service = new StudentsExportService();
$clothes_service = new ClothesService($database);
$requests_service = new RequestsService($database);

$pages_controller = new PagesController();
$students_controller = new StudentsController($students_service, $students_import_service, $students_export_service);
$authentication_controller = new AuthenticationController($users_service, $students_service, $requests_service);
$admin_controller = new AdminController($users_service, $requests_service, $students_service);
$user_controller = new UserController($users_service, $students_service, $clothes_service, $ceremonies_attendance_service);
$ceremonies_controller = new CeremoniesController(
    $ceremonies_service,
    $ceremonies_attendance_service,
    $ceremony_students_export_service,
    $students_service
);

$authorization_middleware = new AuthorizationMiddleware($users_service);
$clothes_controller = new ClothesController($clothes_service);

$base_path = parse_url($_ENV["BASE_URL"])["path"];
$requested_uri = parse_url(trim(str_replace($base_path, "", $_SERVER['REQUEST_URI']), "/"), PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

$router->register_route('GET', '/', function ($params) use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route('GET', '', function ($params) use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route(
    'GET',
    'register',
    $authorization_middleware->is_not_authenticated(function ($params) use ($authentication_controller) {
        $authentication_controller->show_register_page();
    })
);

$router->register_route(
    'POST',
    'register',
    $authorization_middleware->is_not_authenticated(function ($params) use ($authentication_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $authentication_controller->register($data);
            session_start();
            setcookie(session_name(), session_id(), time() + 24 * 60 * 60, '/');

            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    })
);

$router->register_route(
    'GET',
    'login',
    $authorization_middleware->is_not_authenticated(function ($params) use ($authentication_controller) {
        $authentication_controller->show_login_page();
    })
);

$router->register_route(
    'GET',
    'login/pick-fn',
    $authorization_middleware->is_authorized(Role::Student, function ($params) use ($authentication_controller) {
        $authentication_controller->show_pick_fn_page();
    }, false)
);

$router->register_route(
    'POST',
    'login/pick-fn',
    $authorization_middleware->is_authorized(Role::Student, function ($params) use ($authentication_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $authentication_controller->set_fn($data);

            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }, false)
);

$router->register_route(
    'POST',
    'login',
    $authorization_middleware->is_not_authenticated(function ($params) use ($authentication_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $user = $authentication_controller->login($data);

            session_start();
            setcookie(session_name(), session_id(), time() + 24 * 60 * 60, '/');
            
            echo json_encode($user->to_array(), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    })
);

$router->register_route(
    'GET',
    'students/import',
    $authorization_middleware->is_authorized(Role::Administrator, function ($params) use ($students_controller) {
        $students_controller->show_import_students_page();
    })
);

$router->register_route(
    'POST',
    'students/import',
    $authorization_middleware->is_authorized(Role::Administrator, function ($params) use ($students_controller) {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $students_controller->import_students($data);

            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    })
);

$router->register_route(
    'GET',
    'students/export',
    $authorization_middleware->is_authorized(Role::Administrator, function ($params) use ($students_controller) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=students.csv');
        $students_controller->export_students();
    })
);

$router->register_route(
    'GET',
    'students',
    $authorization_middleware->is_authorized(Role::Administrator, function ($params) use ($students_controller) {
        $students_controller->show_students_page();
    })
);

$router->register_route(
    'GET',
    'admin/approval/administrators',
    $authorization_middleware->is_authorized(Role::Admin, function ($params) use ($admin_controller) {
        $admin_controller->show_administrator_approval_page();
    })
);

$router->register_route(
    'GET',
    'admin/approval/students',
    $authorization_middleware->is_authorized(Role::Admin, function ($params) use ($admin_controller) {
        $admin_controller->show_students_approval_page();
    })
);

$router->register_route(
    'GET',
    'ceremonies/create',
    $authorization_middleware->is_authorized(Role::Administrator, function ($params) use ($ceremonies_controller) {
        $ceremonies_controller->show_create_ceremony_page();
    })
);

$router->register_route(
    'POST',
    'ceremonies/create',
    $authorization_middleware->is_authorized(Role::Administrator, function ($params) use ($ceremonies_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $ceremonies_controller->create_ceremony($data);

            http_response_code(200);
            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    })
);

$router->register_route(
    'GET',
    'profile',
    $authorization_middleware->is_authenticated(function ($params) use ($user_controller) {
        return $user_controller->show_profile_page();
    })
);

$router->register_route(
    'GET',
    'logout',
    $authorization_middleware->is_authenticated(function ($params) {
        session_unset();
        session_destroy();
        header("Location: {$_ENV["BASE_URL"]}/login");
    }, false)
);

$router->register_route(
    'GET',
    'access-denied',
    function ($params) use ($pages_controller) {
        return $pages_controller->show_access_denied_page();
    }
);

$router->register_route(
    'GET',
    'not-approved',
    function () use ($pages_controller) {
        return $pages_controller->show_not_approved_page();
    }
);

$router->register_route(
    'PATCH',
    'clothes',
    $authorization_middleware->is_authorized(Role::Student, function ($params) use ($clothes_controller) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $clothes_controller->assign_clothing($data);

            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    })
);

$router->register_route(
    'PATCH',
    'ceremonies/attendance',
    $authorization_middleware->is_authorized(Role::Student, function () use ($ceremonies_controller) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            $ceremonies_controller->update_ceremony_invitation($data);
            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "{$e->getMessage()}"], JSON_UNESCAPED_UNICODE);
        }
    })
);

$router->register_route(
    'PATCH',
    'ceremonies/attendance/speach',
    $authorization_middleware->is_authorized(Role::Student, function () use ($ceremonies_controller) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $ceremony_id = intval($data["ceremony_id"]);
            $status = SpeachStatus::tryFrom($data["status"]);
            
            session_start();
            $student_fn = $_SESSION["fn"];

            $ceremonies_controller->update_speach_status($ceremony_id, $student_fn, $status);
            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "{$e->getMessage()}"], JSON_UNESCAPED_UNICODE);
        }
    })
);

$router->register_route(
    'PATCH',
    'ceremonies/attendance/responsibility',
    $authorization_middleware->is_authorized(Role::Student, function () use ($ceremonies_controller) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $ceremony_id = intval($data["ceremony_id"]);
            $status = ResponsibilityStatus::tryFrom($data["status"]);

            session_start();
            $student_fn = $_SESSION["fn"];

            $ceremonies_controller->update_responsibility_status($ceremony_id, $student_fn, $status);
            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "{$e->getMessage()}"], JSON_UNESCAPED_UNICODE);
        }
    })
);
$router->register_route(
    'GET', 
    'ceremonies',
    $authorization_middleware->is_authorized(Role::Administrator, 
     function ($params) use ($ceremonies_controller) {
    $ceremonies_controller->show_ceremonies_list_page();
}));

$router->register_route(
    'GET', 
    'ceremony/edit/{id}',
    $authorization_middleware->is_authorized(Role::Administrator, 
        function ($params) use ($ceremonies_controller) {
            $ceremonies_controller->show_ceremonies_edit_page($params["id"]);
}));

$router->register_route(
    'PUT', 
    'ceremony/edit/{id}',
    $authorization_middleware->is_authorized(Role::Administrator, 
        function ($params) use ($ceremonies_controller) {
            try {
                header('Content-Type: application/json');
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                $data["id"] = $params["id"];
    
                $ceremonies_controller->update_ceremony($data);

                http_response_code(200);
                echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["message" => "{$e->getMessage()}"], JSON_UNESCAPED_UNICODE);
            }
}));

$router->register_route(
    'POST',
    'add-fn',
    $authorization_middleware->is_authorized(Role::Student, function () use ($authentication_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $authentication_controller->add_fn($data);

            echo json_encode(["message" => "Success"], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "{$e->getMessage()}"], JSON_UNESCAPED_UNICODE);
        }
    }, false)
);

$router->register_route(
    'GET', 
    'ceremony/students/{id}',
    $authorization_middleware->is_authorized(Role::Administrator, 
        function ($params) use ($ceremonies_controller) {
            $ceremonies_controller->show_ceremonies_students_page($params["id"]);
}));

$router->register_route(
    'GET', 
    'ceremony/students/{id}/export',
    $authorization_middleware->is_authorized(Role::Administrator, 
        function ($params) use ($ceremonies_controller) {
            try {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=students.csv');
                $ceremonies_controller->export_students($params["id"]);  
            }
            catch(Exception $e) {
                http_response_code(500);
                echo json_encode(["message" => "{$e->getMessage()}"], JSON_UNESCAPED_UNICODE);
            }
}));

$router->dispatch($request_method, $requested_uri);
?>