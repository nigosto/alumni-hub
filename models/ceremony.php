<?php
require_once __DIR__ . "/imodel.php";

class Ceremony implements IModel
{
    private $data;
    private $id;


    function __construct($data, $id = null)
    {
        $this->data = $data;
        $this->id = $id;
    }

    public function to_insert_array()
    {
        return [
            "data" => $this->data
        ];
    }
}
?>