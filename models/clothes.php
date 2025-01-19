<?php
require_once __DIR__ . "/imodel.php";

class Clothes implements IModel
{
    private $id;
    private $size;
    private $student_fn;

    function __construct($id, $size, $student_fn=null)
    {
        $this->id = $id;
        $this->size = $size;
        $this->student_fn = $student_fn;
    }
    function get_id()
    {
        return $this->id;
    }
    public function to_array()
    {
        return [
            "size" => $this->size,
            "student_fn" => $this->student_fn,
        ];
    }
}
?>