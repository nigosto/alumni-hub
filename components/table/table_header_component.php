<?php
require_once __DIR__ . "/../component.php";

class TableHeaderComponent extends Component {
    private $values;

    function __construct($values) {
        $this->values = $values;
    }

    public function render() {
        return <<<HTML
        <thead>
            <tr>
                {$this->render_cells()}
            </tr>
        </thead>
        HTML; 
    }

    private function render_cells() {
        $html = "";

        foreach ($this->values as $value) {
            $html .= "<th class=\"report-table-cell\">$value</th>";
        }

        return $html;
    }
}
?>