<?php
require_once __DIR__ . "/../models/clothes.php";
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
class ClothesService extends DataService
{
    function __construct(Database $database)
    {
        parent::__construct($database, Clothes::class);
    }
    function get_clothing_for_student($student_fn)
    {
        $get_query = <<<GQ
            SELECT * FROM Clothes where student_fn=:student_fn;
        GQ;
        $data = ["student_fn" => $student_fn];

        return parent::get_with_query($get_query, $data);
    }

    function get_clothes_with_size()
    {
        $get_query = <<<GQ
            SELECT size, COUNT(*) AS occurrences FROM Clothes 
            WHERE student_fn IS NULL
            GROUP BY size;
        GQ;

        $get_fn_func = function ($data) {
            return [$data["size"] => $data["occurrences"]];
        };

        return array_merge(...parent::find_all_with_query_map($get_query, null, $get_fn_func));
    }

    function assign_clothing($size, $fn)
    {
        $update_query = <<<UQ
        UPDATE Clothes
        SET student_fn = :student_fn
        WHERE id = (
            SELECT id
            FROM clothes
            WHERE size = :size AND student_fn IS NULL
            LIMIT 1
        );
        UQ;

        $data = ["size" => $size, "student_fn" => $fn];
        return parent::update_with_query($update_query, $data);
    }
}
?>