<?php
abstract class Component
{
    public abstract function render();
    public function get_stylesheets()
    {
        return [];
    }
    public function get_scripts()
    {
        return [];
    }
}
?>