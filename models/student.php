<?php
require_once __DIR__ . "/imodel.php";

enum Degree: string
{
    case Bachelor = "bachelor";
    case Master = "master";
    case Doctor = "doctor";
}

function parse_degree($degree)
{
    $degree = mb_strtolower($degree);
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

class Student implements IModel
{
    private $fn;
    private $degree;
    private $fullname;
    private $graduation_year;
    private $grade;
    private $user_id;

    function __construct($fn, $degree, $fullname, $graduation_year, $grade, $user_id)
    {
        $this->fn = $fn;
        $this->degree = Degree::tryFrom($degree) ?? parse_degree($degree);
        $this->fullname = $fullname;
        $this->graduation_year = $graduation_year;
        $this->grade = $grade;
        $this->user_id = $user_id;
    }

    public function to_insert_array()
    {
        return [
            "fn" => $this->fn,
            "degree" => $this->degree->value,
            "fullname" => $this->fullname,
            "graduation_year" => intval($this->graduation_year),
            "grade" => floatval($this->grade),
            "user_id" => $this->user_id,
        ];
    }
}
?>