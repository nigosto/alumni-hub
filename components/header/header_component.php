<?php
require_once __DIR__ . "/../component.php";
require_once __DIR__ . "/../../models/user.php";

class HeaderComponent extends Component
{
    public function render()
    {
        $base_url = $_ENV["BASE_URL"];

        $navigation_items = $this->render_navigation_items();
        return <<<HTML
        <header id="site-header">
            <h2><a href="$base_url">Alumni Hub</a></h2>
            <nav id="site-navigation">
                <ul>
                    $navigation_items
                </ul>
            </nav>
        </header>
        HTML;
    }

    public static function get_stylesheets()
    {
        return [$_ENV["BASE_URL"] . "/components/header/styles.css"];
    }

    private function render_navigation_items()
    {
        session_start();
        $base_url = $_ENV["BASE_URL"];
        $role = $_SESSION["role"];

        if (!isset($role)) {
            return <<<HTML
                <li><a href="$base_url">Начало</a></li>
                <li><a href="$base_url/register">Регистрация</a></li>
                <li><a href="$base_url/login">Вход</a></li>
            HTML;
        }

        $html = "";
        if ($role === Role::Administrator) {
            $html .= <<<HTML
                <li><a href="$base_url/students">Преглед на студентите</a></li>
                <li><a href="$base_url/students/import">Добавяне на студенти</a></li>
                <li><a href="$base_url/ceremonies/create">Създаване на церемония</a></li>
            HTML;
        }

        if ($role === Role::Admin) {
            $html .= <<<HTML
                <li><a href="$base_url/admin/approval/administrators">Преглед на администратори</a></li>
                <li><a href="$base_url/admin/approval/students">Преглед на студенти</a></li>
            HTML;
        }

        if ($role === Role::Student) {
            $html .= "<li><a href=\"$base_url/login/pick-fn\">Смяна на ФН</a></li>";
        }

        return $html . <<<HTML
            <li><a href="$base_url/profile">Профил</a></li>
            <li><a href="$base_url/logout">Изход</a></li>
        HTML;
    }
}
?>