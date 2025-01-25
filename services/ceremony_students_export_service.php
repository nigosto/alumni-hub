<?php
require_once __DIR__ . "/export_service.php";
require_once __DIR__ . "/../models/ceremony_attendance.php";

class CeremonyStudentsExportService extends ExportService {
    public function export($students) {
        parent::export_as_csv(CeremonyAttendance::labels_ceremony_students_list(), $students);
    }
}

?>