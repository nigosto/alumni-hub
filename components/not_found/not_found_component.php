<?php
require_once __DIR__ . "/../component.php";

class NotFoundComponent extends Component
{
    function __construct()
    {}

    public function render()
    {
        $not_found = $_ENV["BASE_URL"] . "/not-found";
        return <<<HTML
        <script>
        window.location.href = "{$not_found}";
        </script>
        HTML;
    }
}
?>