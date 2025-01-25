<?php
require_once __DIR__ . "/imodel.php";

class Request implements IModel {
    private $user_id;
    private $student_fn;

    function __construct($user_id, $student_fn)
    {
        $this->user_id = $user_id;
        $this->student_fn = $student_fn;
    }

    public function to_array() {
        return [
            "user_id" => $this->user_id,
            "student_fn" => $this->student_fn,
        ];
    }
}
?>