<?php
require_once __DIR__ . "/index.php";

class LinkComponent extends ButtonComponent {
    private $href;

    function __construct($value, $href, ButtonStyleType $type = ButtonStyleType::Primary, $classes = null, $data_param = null) {
        parent::__construct($value, $type, false, $classes, $data_param);
        $this->href = $href;
    }

    public function render() {
        $class = $this->type->value . " " . $this->classes ?? "";
        $data_param = isset($this->data_param) ? "data-param=\"$this->data_param\"" : "";

        return "<a href=\"$this->href\" $data_param class=\"$class\">$this->value</a>";
    }
}
?>