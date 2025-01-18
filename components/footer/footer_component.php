<?php
require_once __DIR__ . "/../component.php";

class FooterComponent extends Component
{
    public function render()
    {
        $year = date("Y");
        return <<<HTML
        <footer id="site-footer">
            <p>@Alumni Hub, $year</p>
        </footer>
        HTML;
    }

    public function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/footer/styles.css"];
    }
}
?>