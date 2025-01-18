<?php
require_once __DIR__ . "/imodel.php";

class User implements IModel
{
    private $email;
    private $password;
    private $username;
    private $role;
    private $id;


    function __construct($email, $password, $username, $role, $id = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->role = $role;
        $this->id = $id;
    }

    public function to_array()
    {
        return [
            "email" => $this->email,
            "password" => $this->password,
            "username" => $this->username,
            "role" => $this->role
        ];
    }
}
?>