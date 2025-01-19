<?php
require_once __DIR__ . "/../component.php";

class AdministratorHeaderComponent extends Component
{
    public function render()
    {
        $base_url = $_ENV["BASE_URL"];

        return <<<HTML
        <header id="site-header">
            <h2><a href="$base_url">Alumni Hub</a></h2>
            <nav id="site-navigation">
                <ul>
                    <li><a href="$base_url/administrator">Начало</a></li>
                    <li><a href="$base_url/ceremonies">Преглед на церемониите</a></li>
                    <li><a href="$base_url/ceremonies/create">Създаване на церемония</a></li>
                    <li><a href="$base_url/logout">Изход</a></li>
                </ul>
            </nav>
        </header>
        HTML;
    }

    public static function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/header/styles.css"];
    }
}
?>