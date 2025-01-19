<?php
require_once __DIR__ . "/../component.php";
require_once __DIR__ . "/table_header_component.php";
require_once __DIR__ . "/table_row_component.php";

class TableComponent extends Component
{
    private $header_values;
    private $row_values;
    private $action;
    private $action_value;
    private $disable;

    function __construct($header_values, $row_values, $action_value = null, $action = null, $disable = null)
    {
        $this->header_values = $header_values;
        $this->row_values = $row_values;
        $this->action = $action;
        $this->action_value = $action_value;
        $this->disable = $disable;
    }

    public function render()
    {
        $values = $this->header_values;
        if ($this->action_value) {
            $values[] = "Действие";
        }

        $header = new TableHeaderComponent($values);

        return <<<HTML
        <table class="report-table">
            {$header->render()}
            <tbody>
                {$this->render_rows()}
            </tbody>
        </table>
        HTML;
    }

    private function render_rows()
    {
        $html = "";

        foreach ($this->row_values as $row) {
            $component = new TableRowComponent(
                $row,
                $this->action_value,
                $this->action ? call_user_func($this->action, $row) : null,
                $this->disable ? call_user_func($this->disable, $row) : null
            );
            $html .= $component->render();
        }

        return $html;
    }

    public static function get_stylesheets()
    {
        return [...TableRowComponent::get_stylesheets(), $_ENV["BASE_URL"] . "/components/table/styles.css"];
    }
}
?>