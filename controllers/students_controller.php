<?php
class StudentsController
{
    private $students_service;
    private $students_import_service;

    function __construct($students_service, $students_import_service)
    {
        $this->students_service = $students_service;
        $this->students_import_service = $students_import_service;
    }

    public function show_students_page()
    {
        require_once __DIR__ . "/../pages/students/index.php";
    }

    public function show_import_students_page()
    {
        require_once __DIR__ . "/../pages/import-students/index.php";
    }

    public function import_students()
    {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $students = $this->students_import_service->parse_csv_as_base64($data->file);
            $this->students_service->insert_many($students);

            echo json_encode(["Message" => "Success"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["Message" => "Fail: {$e->getMessage()}"]);
        }
    }
}
?>