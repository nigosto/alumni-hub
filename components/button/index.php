<?php
require_once __DIR__ . "/../base.php";
require_once __DIR__ . "/../../config.php";

enum ButtonStyleType: string
{
    case Primary = "primary-btn";
    case Secondary = "secondary-btn";
}

class ButtonComponent extends Component
{
    protected $value;
    protected ButtonStyleType $type;

    function __construct($value, ButtonStyleType $type = ButtonStyleType::Primary)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function render()
    {
        $class = $this->type->value;
        return "<button class=\"$class\">$this->value</button>";
    }

    public static function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/button/styles.css"];
    }
}

?>