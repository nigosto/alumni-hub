<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../models/ceremony_attendance.php";

class CeremoniesAttendanceService extends DataService
{
    function __construct(Database $database)
    {
        parent::__construct($database, CeremonyAttendance::class);
    }
}
?>