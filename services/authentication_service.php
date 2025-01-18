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
            INSERT INTO USERS (email, password, username, role)  VALUES (:email, :password, :username, :role)
        IQ;

        return parent::insert_with_query($insert_query, $user);
    }
    function get_user($username)
    {
        $get_query = <<<IQ
            Select * from Users where username=:username
        IQ;
        $data = ["username" => $username];
        return parent::get_with_query($get_query, $data);
    }
}
?>