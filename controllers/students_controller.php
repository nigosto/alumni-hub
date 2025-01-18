<?php
require_once __DIR__ . "/../services/students_service.php";
require_once __DIR__ . "/../services/students_import_service.php";

class StudentsController
{
    private StudentsService $students_service;
    private StudentsImportService $students_import_service;

    function __construct($students_service, $students_import_service)
    {
        $this->students_service = $students_service;
        $this->students_import_service = $students_import_service;
    }

    public function show_students_page()
    {
        $controller = $this;
        require_once __DIR__ . "/../pages/students/index.php";
    }

    public function show_import_students_page()
    {
        require_once __DIR__ . "/../pages/import-students/index.php";
    }

    public function import_students($data)
    {
        $students = $this->students_import_service->parse_csv_as_base64($data->file);
        $this->students_service->insert_many($students);
    }

    public function get_students_data()
    {
        return array_map(function ($student) {
            $values = array_values($student->to_array(true));
            array_pop($values);
            return $values;
        }, $this->students_service->find_all());
    }
}
?>