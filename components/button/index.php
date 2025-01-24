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
    protected $classes;
    protected $data_param;
    private $input;

    function __construct($value, ButtonStyleType $type = ButtonStyleType::Primary, $input = false, $classes = null, $data_param = null)
    {
        $this->value = $value;
        $this->type = $type;
        $this->input = $input;
        $this->classes = $classes;
        $this->data_param = $data_param;
    }

    public function render()
    {
        $class = $this->type->value . " " . $this->classes ?? "";
        $data_param = isset($this->data_param) ? "data-param=\"$this->data_param\"" : "";

        if ($this->input) {
            return "<input type=\"submit\" class=\"$class\" $data_param value=\"$this->value\" />";
        }

        return "<button $data_param class=\"$class\">$this->value</button>";
    }

    public static function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/button/styles.css"];
    }
}

?>