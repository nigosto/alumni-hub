<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/utils/query_params.php";
require_once __DIR__ . "/router.php";
require_once __DIR__ . "/controllers/pages_controller.php";
require_once __DIR__ . "/controllers/students_controller.php";
require_once __DIR__ . "/controllers/authentication_controller.php";
require_once __DIR__ . "/controllers/admin_controller.php";
require_once __DIR__ . "/controllers/user_controller.php";
require_once __DIR__ . "/database/database.php";
require_once __DIR__ . "/services/students_service.php";
require_once __DIR__ . "/services/students_import_service.php";
require_once __DIR__ . "/services/students_export_service.php";
require_once __DIR__ . "/services/users_service.php";
require_once __DIR__ . "/middleware/authorization_middleware.php";
require_once __DIR__ . "/models/user.php";

load_config(".env");

$router = new Router();
$database = new Database();

$students_service = new StudentsService($database);
$users_service = new UsersService($database);
$students_import_service = new StudentsImportService();
$students_export_service = new StudentsExportService();

$pages_controller = new PagesController();
$students_controller = new StudentsController($students_service, $students_import_service, $students_export_service);
$authentication_controller = new AuthenticationController($users_service, $students_service);
$admin_controller = new AdminController($users_service);
$user_controller = new UserController($users_service, $students_service);

$authorization_middleware = new AuthorizationMiddleware();

$base_path = parse_url($_ENV["BASE_URL"])["path"];
$requested_uri = parse_url(trim(str_replace($base_path, "", $_SERVER['REQUEST_URI']), "/"), PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

$router->register_route('GET', '/', function () use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route('GET', '', function () use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route(
    'GET',
    'register',
    $authorization_middleware->is_not_authenticated(function () use ($authentication_controller) {
        $authentication_controller->show_register_page();
    })
);

$router->register_route(
    'POST',
    'register',
    $authorization_middleware->is_not_authenticated(function () use ($authentication_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $authentication_controller->register($data);

            echo json_encode(["Message" => "Success"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["Message" => "Fail: {$e->getMessage()}"]);
        }
    })
);

$router->register_route(
    'GET',
    'login',
    $authorization_middleware->is_not_authenticated(function () use ($authentication_controller) {
        $authentication_controller->show_login_page();
    })
);

$router->register_route(
    'GET',
    'login/pick-fn',
    $authorization_middleware->is_authorized(Role::Student, function () use ($authentication_controller) {
        $authentication_controller->show_pick_fn_page();
    })
);

$router->register_route(
    'POST',
    'login/pick-fn',
    $authorization_middleware->is_authorized(Role::Student, function () use ($authentication_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $authentication_controller->set_fn($data);

            echo json_encode(["Message" => "Success"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["Message" => "Fail: {$e->getMessage()}"]);
        }
    })
);

$router->register_route(
    'POST',
    'login',
    $authorization_middleware->is_not_authenticated(function () use ($authentication_controller) {
        try {
            header('Content-Type: application/json');
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $user = $authentication_controller->login($data);

            echo json_encode($user->to_array());
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["Message" => "Fail: {$e->getMessage()}"]);
        }
    })
);

$router->register_route(
    'GET',
    'students/import',
    $authorization_middleware->is_authorized(Role::Administrator, function () use ($students_controller) {
        $students_controller->show_import_students_page();
    })
);

$router->register_route(
    'POST',
    'students/import',
    $authorization_middleware->is_authorized(Role::Administrator, function () use ($students_controller) {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $students_controller->import_students($data);

            echo json_encode(["Message" => "Success"]);
        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode(["Message" => "Some of the students are already imported"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["Message" => "Fail: {$e->getMessage()}"]);
        }
    })
);

$router->register_route(
    'GET',
    'students/export',
    $authorization_middleware->is_authorized(Role::Administrator, function () use ($students_controller) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=students.csv');
        $students_controller->export_students();
    })
);

$router->register_route(
    'GET',
    'students',
    $authorization_middleware->is_authorized(Role::Administrator, function () use ($students_controller) {
        $students_controller->show_students_page();
    })
);

$router->register_route(
    'GET',
    'admin/approval',
    $authorization_middleware->is_authorized(Role::Admin, function () use ($admin_controller) {
        $admin_controller->show_approval_page();
    })
);

$router->register_route(
    'GET',
    'profile',
    $authorization_middleware->is_authenticated(function () use ($user_controller) {
        return $user_controller->show_profile_page();
    })
);

$router->register_route(
    'GET',
    'logout',
    $authorization_middleware->is_authenticated(function () {
        session_destroy();
        header("Location: {$_ENV["BASE_URL"]}/login");
    })
);

$router->dispatch($request_method, $requested_uri);
?>