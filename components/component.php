<?php
abstract class Component
{
    public abstract function render();
    public static function get_stylesheets()
    {
        return [];
    }
    public static function get_scripts()
    {
        return [];
    }
}
?>