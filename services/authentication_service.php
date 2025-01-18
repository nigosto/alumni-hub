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

}
?>