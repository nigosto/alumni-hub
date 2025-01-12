<?php
require_once __DIR__ . "/../base.php";
require_once __DIR__ . "/../../config.php";

class FooterComponent extends Component
{
    public function render()
    {
        $year = date("Y");
        return <<<HTML
        <footer id="site-footer">
            <p>@Alumni Hub, $year</p>
        </header>
        HTML;
    }

    public function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/footer/styles.css"];
    }
}
?>