<?php
require_once __DIR__ . "/../component.php";

class TableRowComponent extends Component {
    private $values;

    function __construct($values) {
        $this->values = $values;
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
            $html .= "<td class=\"report-table-cell\">$value</td>";
        }

        return $html;
    }
}
?>