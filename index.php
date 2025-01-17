<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/utils/query_params.php";
require_once __DIR__ . "/router.php";
require_once __DIR__ . "/controllers/pages_controller.php";
require_once __DIR__ . "/controllers/students_controller.php";
require_once __DIR__ . "/database/database.php";
require_once __DIR__ . "/services/students_service.php";
require_once __DIR__ . "/services/students_import_service.php";

load_config(".env");

$router = new Router();
$database = new Database();

$students_service = new StudentsService($database);
$students_import_service = new StudentsImportService();

$pages_controller = new PagesController();
$students_controller = new StudentsController($students_service, $students_import_service);

$base_path = parse_url($_ENV["BASE_URL"])["path"];
$requested_uri = parse_url(trim(str_replace($base_path, "", $_SERVER['REQUEST_URI']), "/"), PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

$router->register_route('GET', '/', function() use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route('GET', '', function() use ($pages_controller) {
    $pages_controller->show_home_page();
});

$router->register_route('GET', 'students/import', function() use ($students_controller) {
    $students_controller->show_import_students_page();
});

$router->register_route('POST', 'students/import', function() use ($students_controller) {
    $students_controller->import_students();
});

$router->register_route('GET', 'students', function() use ($students_controller) {
    $students_controller->show_students_page();
});

$router->dispatch($request_method, $requested_uri);
?>