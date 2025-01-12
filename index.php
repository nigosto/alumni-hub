<?php
require_once __DIR__ . "/config.php";

$base_path = parse_url($_ENV["BASE_URL"])["path"];
$requested_page = trim(str_replace($base_path, "", $_SERVER['REQUEST_URI']), "/");

if (empty($requested_page)) {
    $requested_page = "home";
}

$pages_directory = __DIR__ . "/pages";
$requested_file = $pages_directory . "/" . $requested_page . "/index.php";

if (file_exists($requested_file)) {
    include $requested_file;
}
?>