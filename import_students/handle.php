<?php
require __DIR__ . "/../services/import.php";

$data = json_decode(file_get_contents("php://input"));
echo json_encode($import_service->parse_base64_to_csv($data->file), JSON_UNESCAPED_UNICODE);
?>