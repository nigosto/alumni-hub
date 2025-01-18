<?php
require_once __DIR__ . "/imodel.php";

class Ceremony implements IModel
{
    private $date;
    private $id;
    private $speach_status;
    private $responsibility_status;

    function __construct($date, $id = null)
    {
        $this->date = $date;
        $this->id = $id;
    }

    public function to_insert_array()
    {
        return [
            "date" => $this->date
        ];
    }
}
?>