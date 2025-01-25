<?php
require_once __DIR__ . "/imodel.php";

class Ceremony implements IModel
{
    private $date;
    private $graduation_year;
    private $id;

    function __construct($date, $graduation_year, $id = null)
    {
        $this->date = $date;
        $this->graduation_year = $graduation_year;
        $this->id = $id;
    }

    public function to_array()
    {
        return [
            "id" => $this->id,
            "date" => $this->date->format("Y-m-d H:i:s"),
            "graduation_year" => $this->graduation_year,
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