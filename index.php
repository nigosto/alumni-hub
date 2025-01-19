<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/utils/query_params.php";
require_once __DIR__ . "/router.php";
require_once __DIR__ . "/controllers/pages_controller.php";
require_once __DIR__ . "/controllers/students_controller.php";
require_once __DIR__ . "/controllers/ceremonies_controller.php";
require_once __DIR__ . "/controllers/authentication_controller.php";
require_once __DIR__ . "/database/database.php";
require_once __DIR__ . "/services/students_service.php";
require_once __DIR__ . "/services/ceremonies_service.php";
require_once __DIR__ . "/services/ceremonies_attendance_service.php";
require_once __DIR__ . "/services/students_import_service.php";
require_once __DIR__ . "/services/students_export_service.php";
require_once __DIR__ . "/services/authentication_service.php";

load_config(".env");

$router = new Router();
$database = new Database();

$students_service = new StudentsService($database);
$authentication_service = new AuthenticationService($database);
$students_import_service = new StudentsImportService();
$ceremoinies_service = new CeremoniesService($database);
$ceremonies_attendance_service = new CeremoniesAttendanceService($database, $students_service);
$students_export_service = new StudentsExportService();

$pages_controller = new PagesController();
$students_controller = new StudentsController($students_service, $students_import_service, $students_export_service);
$authentication_controller = new AuthenticationController($authentication_service, $students_service);
$ceremonies_controller = new CeremoniesController(
    $ceremoinies_service, $ceremonies_attendance_service, $students_service);

$base_path = parse_url($_ENV["BASE_URL"])["path"];
$requested_uri = parse_url(trim(str_replace($base_path, "", $_SERVER['REQUEST_URI']), "/"), PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

$router->register_route('GET', '/', function () use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route('GET', '', function () use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route('GET', 'register', function () use ($authentication_controller) {
    $authentication_controller->show_register_page();
});

$router->register_route('POST', 'register', function () use ($authentication_controller) {
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

});

$router->register_route('GET', 'students/import', function () use ($students_controller) {
    $students_controller->show_import_students_page();
});

$router->register_route('POST', 'students/import', function () use ($students_controller) {
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
});

$router->register_route('GET', 'students/export', function() use ($students_controller) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=students.csv');
    $students_controller->export_students();
});

$router->register_route('GET', 'students', function () use ($students_controller) {
    $students_controller->show_students_page();
});

$router->register_route('GET', 'ceremonies/create', function () use ($ceremonies_controller) {
    // TODO: Authorization
    $ceremonies_controller->show_create_ceremony_page();
});

$router->register_route('POST', 'ceremonies/create', function () use ($ceremonies_controller) {
    try {
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $ceremonies_controller->create_ceremony($data);

        http_response_code(200);
        echo json_encode(["Message" => "Success"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["Message" => "Fail: {$e->getMessage()}"]);
    }
});

$router->dispatch($request_method, $requested_uri);
?>