<?php
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";
class AuthenticationService extends DataService
{
    function __construct(Database $database)
    {
        parent::__construct($database);
    }

    function insert_many($users)
    {
        $insert_query = <<<IQ
            INSERT INTO USERS (email, password, username, role)  VALUES (:email, :password, :username, :role)
        IQ;

        parent::insert_many_with_query($insert_query, $users);
    }

}
?>