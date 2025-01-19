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

    public function to_array()
    {
        return [
            "date" => $this->date->format("Y-m-d H:i:s"),
        ];
    }

    public static function labels()
    {
        return [
            "Дата на завършване", 
            "Дата на церемонията", 
            "Студент, изнасящ церемониална реч", 
            "Студент отговорник за тоги", 
            "Студент отговорник за подписи",
            "Студент отговорник за връчване на дипломи"
        ];
    }
}
?>