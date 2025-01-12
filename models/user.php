<?php
require __DIR__ . "/imodel.php";

class User implements IModel
{
    private $email;
    private $password;
    private $username;
    private $role;

    function __construct($email, $password, $username, $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->role = $role;
    }

    public function to_insert_array()
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