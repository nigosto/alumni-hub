<?php
class PagesController
{
    public function show_home_page()
    {
        require_once __DIR__ . "/../pages/home/index.php";
    }

    public function show_access_denied_page()
    {
        require_once __DIR__ . "/../pages/access_denied/index.php";
    }

    public function show_not_approved_page()
    {
        require_once __DIR__ . "/../pages/not_approved/index.php";
    }
}
?>