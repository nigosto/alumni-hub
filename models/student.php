<?php
require __DIR__ . "/imodel.php";

enum Degree: string {
    case Bachelor = "bachelor";
    case Master = "master";
    case Doctor = "doctor";
}

function degree_to_string(Degree $degree) {
    switch ($degree) {
        case Degree::Bachelor:
            return "bachelor";
        case Degree::Master:
            return "master";
        case Degree::Doctor:
            return "doctor";
    }
}

function parse_degree($degree) {
    $degree = mb_strtolower($degree);
    echo $degree . "\n";
    switch ($degree) {
        case 'бакалавър':
            return Degree::Bachelor;
        case 'магистър':
            return Degree::Master;
        case 'доктор':
            return Degree::Doctor;
        default:
            throw new Exception("Invalid degree");
    }
}

class Student implements IModel {
    private $fn;
    private $degree;
    private $fullname;
    private $graduation_year;
    private $grade;
    private $user_id;

    function __construct($fn, $degree, $fullname, $graduation_year, $grade, $user_id) {
        try {
            $this->fn = $fn;
            $this->degree = parse_degree($degree);
            $this->fullname = $fullname;
            $this->graduation_year = $graduation_year;
            $this->grade = $grade;
            $this->user_id = $user_id;
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function to_insert_array() {
        return [
            "fn" => $this->fn,
            "degree" => degree_to_string($this->degree),
            "fullname" => $this->fullname,
            "graduation_year" => intval($this->graduation_year),
            "grade" => floatval($this->grade),
            "user_id" => $this->user_id,
        ];
    }
}
?>