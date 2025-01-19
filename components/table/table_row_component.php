<?php
require_once __DIR__ . "/../component.php";
require_once __DIR__ . "/../button/link.php";

class TableRowComponent extends Component {
    private $values;
    private $action;
    private $action_value;
    private $disabled;

    function __construct($values, $action_value = null, $action = null, $disabled = null) {
        $this->values = $values;
        $this->action_value = $action_value;
        $this->action = $action;
        $this->disabled = $disabled; 
    }

    public function render() {
        return <<<HTML
        <tr>
            {$this->render_cells()}
        </tr>
        HTML; 
    }

    private function render_cells() {
        $html = "";

        foreach ($this->values as $value) {
            $html .= "<td align=\"center\" class=\"report-table-cell\">$value</td>";
        }

        if ($this->action_value) {            
            if ($this->disabled) {
                $link = new LinkComponent($this->action_value, "#", ButtonStyleType::DisabledSmall);
                $html .= "<td align=\"center\" class=\"report-table-cell\">{$link->render()}</td>";
                return $html;
            }

            $action_key = array_key_first($this->action);
            $action_param = $this->action[$action_key];

            $link = new LinkComponent($this->action_value, "?$action_key=$action_param", ButtonStyleType::Small);
            $html .= "<td align=\"center\" class=\"report-table-cell\">{$link->render()}</td>";
        }

        return $html;
    }

    public static function get_stylesheets()
    {
        return LinkComponent::get_stylesheets();
    }
}
?>