<?php
require_once __DIR__ . "/data_service.php";
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/../models/request.php";

class RequestsService extends DataService {
    function __construct(Database $database)
    {
        parent::__construct($database, Request::class);
    }

    public function insert_request(Request $request) {
        $insert_query = <<<IQ
            INSERT INTO Requests (user_id, student_fn) 
            VALUES (:user_id, :student_fn)
        IQ;

        return parent::insert_with_query($insert_query, $request);
    }

    public function get_requests_data() {
        $query = <<<FQ
            SELECT username, email, fullname, fn FROM `Requests` 
            JOIN Students ON student_fn = fn
            JOIN Users ON Users.id = Requests.user_id
        FQ;

        return parent::find_all_with_query_map($query, [], function ($row) {return $row; });
    }

    public function delete_request($user_id, $student_fn) {
        $query = <<<DQ
            DELETE FROM `Requests`
            WHERE student_fn = :student_fn AND user_id = :user_id
        DQ;

        return parent::delete_with_query($query, ["user_id" => $user_id, "student_fn" => $student_fn]);
    }
}

?>