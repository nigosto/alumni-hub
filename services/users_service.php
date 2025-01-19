<?php
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/data_service.php";

class UsersService extends DataService
{
    function __construct(Database $database)
    {
        parent::__construct($database, User::class);
    }

    public function insert($user)
    {
        $insert_query = <<<IQ
            INSERT INTO Users (email, password, username, role, approved) 
            VALUES (:email, :password, :username, :role, :approved)
        IQ;

        return parent::insert_with_query($insert_query, $user);
    }
    public function get_user($username)
    {
        $get_query = <<<IQ
            Select * from Users where username=:username
        IQ;
        $data = ["username" => $username];
        return parent::get_with_query($get_query, $data);
    }

    public function find_users_by_role(Role $role) {
        $get_query = <<<GQ
            SELECT * FROM Users
            WHERE role=:role
        GQ;

        return parent::find_all_with_query($get_query, ["role" => $role->value]);
    }

    public function approve_user_by_email($email) {
        $update_query = <<<UQ
            UPDATE Users
            SET approved = 1
            WHERE email=:email
        UQ;

        parent::update_with_query($update_query, ["email" => $email]);        
    }
}
?>