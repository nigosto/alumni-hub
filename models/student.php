<?php
class Student {
    public $fn;
    public $degree;
    public $fullname;
    public $graduation_year;
    public $grade;
    public $user_id;

    function __construct($fn, $degree, $fullname, $graduation_year, $grade, $user_id) {
        $this->fn = $fn;
        $this->degree = $degree;
        $this->fullname = $fullname;
        $this->graduation_year = $graduation_year;
        $this->grade = $grade;
        $this->user_id = $user_id;
    }

    public function to_insert_array() {
        return [
            "fn" => $this->fn,
            "degree" => $this->degree,
            "fullname" => $this->fullname,
            "graduation_year" => intval($this->graduation_year),
            "grade" => floatval($this->grade),
            "user_id" => $this->user_id,
        ];
    }
}
?>