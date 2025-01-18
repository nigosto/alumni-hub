<?php
require_once __DIR__ . "/../component.php";
require_once __DIR__ . "/table_header_component.php";
require_once __DIR__ . "/table_row_component.php";

class TableComponent extends Component {
    private $header_values;
    private $row_values;
    
    function __construct($header_values, $row_values) {
        $this->header_values = $header_values;
        $this->row_values = $row_values;
    }

    public function render() {
        $header = new TableHeaderComponent($this->header_values);

        return <<<HTML
        <table class="report-table">
            {$header->render()}
            <tbody>
                {$this->render_rows()}
            </tbody>
        </table>
        HTML;    
    }

    private function render_rows() {
        $html = "";

        foreach ($this->row_values as $row) {
            $component = new TableRowComponent($row);
            $html .= $component->render();
        }

        return $html;
    }

    public static function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/table/styles.css"];
    }
}
?>