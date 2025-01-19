<?php
require_once __DIR__ . "/imodel.php";

enum Role: string {
    case Student = "student";
    case Administrator = "administrator";
    case Admin = "admin";
}

function prettify_role(Role $role)
{
    return match ($role) {
        Role::Student => "Студент",
        Role::Administrator => "Администратор",
        Role::Admin => "Админ",
    };
}


class User implements IModel
{
    private $id;
    private $email;
    private $password;
    private $username;
    private Role $role;
    private $approved;

    function __construct($id, $email, $password, $username, $role, $approved)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->role = Role::tryFrom($role);
        $this->approved = $approved;
    }
    function get_id()
    {
        return $this->id;
    }

    public function to_array($prettify = false)

    {
        if ($prettify) {
            return [
                "email" => $this->email,
                "username" => $this->username,
                "role" => prettify_role($this->role),
                "approved" => $this->approved ? "Да" : "Не"
            ];
        } 

        return [
            "email" => $this->email,
            "password" => $this->password,
            "username" => $this->username,
            "role" => $this->role->value,
            "approved" => intval($this->approved)
        ];
    }

    public static function labels() {
        return ["Имейл","Потребителско име","Роля","Одобрен"];
    }

    public function compare_password($password)
    {
        return password_verify($password, $this->password);
    }
}
?>