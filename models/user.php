<?php
require_once __DIR__ . "/imodel.php";

class User implements IModel
{
    private $id;
    private $email;
    private $password;
    private $username;
    private $role;

    function __construct($id, $email, $password, $username, $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->role = $role;
    }

    public function get_id()
    {
        return $this->id;
    }
    public function get_role()
    {
        return $this->role;
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

    public function compare_password($password)
    {
        return password_verify($password, $this->password);
    }
}
?>