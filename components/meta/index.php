<?php
require_once __DIR__ . "/../base.php";
require_once __DIR__ . "/../../config.php";

class MetadataComponent extends Component {
    private $title;
    private $scripts;
    private $stylesheets;

    function __construct($stylesheets = [], $scripts = [], $title = "Alumni Hub") {
        $this->title = $title;
        $this->scripts = $scripts;
        $this->stylesheets = $stylesheets;
    }

    public function render() {
        return <<<HTML
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>$this->title</title>
            {$this->insert_scripts()}
            {$this->insert_stylesheets()}
        </head>
        HTML;
    }

    private function insert_scripts() {
        $html = "";
        foreach ($this->scripts as $script) {
            $html .= "<script src=\"$script\"></script>\n";
        }

        return $html;
    }

    private function insert_stylesheets() {
        $html = "<link rel=\"stylesheet\" href=\"" . $_ENV["BASE_URL"] . "/styles.css\">\n";
        foreach ($this->stylesheets as $stylesheet) {
            $html .= "<link rel=\"stylesheet\" href=\"$stylesheet\">\n";
        }

        return $html;
    }
}
?>