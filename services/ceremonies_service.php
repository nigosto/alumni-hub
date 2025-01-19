<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../models/ceremony.php";

class CeremoniesService extends DataService
{
    public function __construct(Database $database)
    {
        parent::__construct($database, Ceremony::class);
    }

    public function create_date_from_string($date)
    {
        $format = 'Y-m-d\TH:i';
        return DateTime::createFromFormat($format, $date);
    }

    public function insert_ceremony($ceremony)
    {
        $insert_query = <<<IQ
            INSERT INTO Ceremony (date) VALUES (:date)
        IQ;

        return parent::insert_with_query($insert_query, $ceremony);
    }
}
?>