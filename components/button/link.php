<?php
require_once __DIR__ . "/index.php";

class LinkComponent extends ButtonComponent {
    private $href;

    function __construct($value, $href, ButtonStyleType $type = ButtonStyleType::Primary) {
        parent::__construct($value, $type);
        $this->href = $href;
    }

    public function render() {
        $class = $this->type->value;
        return "<a href=\"$this->href\" class=\"$class\">$this->value</a>";
    }
}
?>