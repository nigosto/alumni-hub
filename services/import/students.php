<?php
require __DIR__ . "/base.php";
require __DIR__ . "/../../models/student.php";

class StudentsImportService extends ImportService
{
    final public function parse_csv_as_base64($file)
    {
        $parsed_file = parent::parse_csv_as_base64($file);

        return array_map(function ($student) use ($parsed_file) {
            $header = $parsed_file["header"];
            
            return new Student(
                $student[$header[0]],
                $student[$header[1]],
                $student[$header[2]],
                $student[$header[3]],
                $student[$header[4]],
                $student[$header[5]]
            );
        }, $parsed_file["data"]);
    }
}

$students_import_service = new StudentsImportService();
?>