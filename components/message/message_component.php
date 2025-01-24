<?php
require_once __DIR__ . "/../component.php";

class MessageComponent extends Component
{
    public function render()
    {
        return <<<HTML
            <div id="popup" class="popup hidden" role="alert" aria-live="assertive"></div>
        HTML;
    }

    public static function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/message/styles.css"];
    }
    public static function get_scripts()
    {
        return [$_ENV["BASE_URL"] . "/components/message/script.js"];
    }
}
?>