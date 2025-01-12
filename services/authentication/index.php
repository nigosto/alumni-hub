<?php
require_once __DIR__ . "/../database/index.php";
require __DIR__ . "/../models/user.php";

class AuthenticationService extends BaseDataService
{

    function insert_many($users)
    {
        $insert_query = <<<IQ
            INSERT INTO USERS (email, password, username, role)  VALUES (:email, :password, :username, :role)
        IQ;

        parent::insert_many_with_query($insert_query, $users);
    }

}

$authentication_service = new AuthenticationService($database);
?>