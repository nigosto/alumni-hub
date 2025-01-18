<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../models/ceremony.php";

class CeremoniesService extends DataService
{
    function __construct(Database $database)
    {
        parent::__construct($database, Ceremony::class);
    }

    function get_ceremony_by_id($id)
    {
        $query = <<<IQ
            SELECT * FROM Ceremony WHERE ceremony_id=:ID
        IQ;

        $data = ["ID" => strval($id)];
        return parent::get_with_query($query, $data);
    }
}
?>