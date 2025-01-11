<?php
// TODO: extract somewhere
$s = str_replace("/alumni-hub", "", $_SERVER['REQUEST_URI']);
$requested_page = trim($s, "/");

if (empty($requested_page)) {
    $requested_page = "home";
}

$pages_directory = __DIR__ . "/pages";
$requested_file = $pages_directory . "/" . $requested_page . "/index.php";

if (file_exists($requested_file)) {
    include $requested_file;
}
?>