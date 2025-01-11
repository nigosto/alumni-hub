<?php
require __DIR__ . "/../services/import/students.php";
require_once __DIR__ . "/../services/students/index.php";

$data = json_decode(file_get_contents("php://input"));
$students = $students_import_service->parse_csv_as_base64($data->file);
$students_service->insert_many($students);

echo json_encode(["Message" => "Success"]);
?>