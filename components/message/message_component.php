<?php
require_once __DIR__ . "/../component.php";

enum MessageVariant: string
{
    case Error = "error-popup";
    case Success = "success-popup";
}

class MessageComponent extends Component
{
    private MessageVariant $variant;

    function __construct(MessageVariant $variant) {
        $this->variant = $variant;
    }

    public function render()
    {
        $variant = $this->variant->value;
        return <<<HTML
            <div id="$variant" class="popup hidden $variant" role="alert" aria-live="assertive"></div>
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