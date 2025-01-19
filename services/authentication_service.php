<?php
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
class AuthenticationService extends DataService
{
    function __construct(Database $database)
    {
        parent::__construct($database, User::class);
    }

    function insert($user)
    {
        $insert_query = <<<IQ
            INSERT INTO Users (email, password, username, role)  VALUES (:email, :password, :username, :role)
        IQ;

        return parent::insert_with_query($insert_query, $user);
    }
    function get_user_by_username($username)
    {
        $get_query = <<<IQ
            Select * from Users where username=:username
        IQ;
        $data = ["username" => $username];
        return parent::get_with_query($get_query, $data);
    }

    function get_user_by_id($id)
    {
        $get_query = <<<IQ
            Select * from Users where id=:id
        IQ;

        $data = ["id" => $id];
        return parent::get_with_query($get_query, $data);
    }
}
?>