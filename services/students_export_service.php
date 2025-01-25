<?php
require_once __DIR__ . "/export_service.php";
require_once __DIR__ . "/../models/student.php";

class StudentsExportService extends ExportService {
    public function export($students) {
        $data = array_map(function ($student) {
            return $student->to_array(true, true);
        }, $students);
        parent::export_as_csv(Student::labels(), $data);
    }
}

?>