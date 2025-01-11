<?php
require_once __DIR__ . "/../base.php";
require_once __DIR__ . "/../../config.php";

class HeaderComponent extends Component {
    public function render() {
        $base_url = $_ENV["BASE_URL"];
        
        return <<<HTML
        <header id="site-header">
            <h2><a href="$base_url">Alumni Hub</a></h2>
            <nav id="site-navigation">
                <ul>
                    <li><a href="$base_url">Начало</a></li>
                    <li><a href="$base_url/import-students">Добавяне на студенти</a></li>
                    <li><a href="#">Регистрация</a></li>
                </ul>
            </nav>
        </header>
        HTML;
    }

    public function get_stylesheets() {
        return [$_ENV["BASE_URL"] . "/components/header/styles.css"];
    }
};
?>