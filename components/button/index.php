<?php
require_once __DIR__ . "/../component.php";

enum ButtonStyleType: string
{
    case Primary = "primary-btn";
    case Secondary = "secondary-btn";
    case Small = "small-btn";
    case DisabledSmall = "small-btn disabled-btn";
}

class ButtonComponent extends Component
{
    protected $value;
    protected ButtonStyleType $type;
    private $input;

    function __construct($value, ButtonStyleType $type = ButtonStyleType::Primary, $input = false)
    {
        $this->value = $value;
        $this->type = $type;
        $this->input = $input;
    }

    public function render()
    {
        $class = $this->type->value;

        if ($this->input) {
            return "<input type=\"submit\" class=\"$class\" value=\"$this->value\" />";
        }

        return "<button class=\"$class\">$this->value</button>";
    }

    public static function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/button/styles.css"];
    }
}

?>