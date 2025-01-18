<?php
if ($argc > 3) {
    throw new Exception("Invalid number of arguments");
}

$option = $argv[2];
$action = $argv[1];
$scripts_locations = __DIR__ . "/../database/migrations/";

$output = null;
$code = 0;
$option = $scripts_locations . $option;

$ext = strtolower(pathinfo($option, PATHINFO_EXTENSION));
if (empty($ext)) {
    if ($option[strlen($option)] === "/" || $option[strlen($option)] === "/") {
        $option = substr($option, 0, -1);
    }

    if ($action === "-r" || $action === "-rollback") {
        $option .= "rollback.php";
    }
    else {
        $option .= "migrate.php";
    }
}

exec("php $option", $output, $code);

if ($code !== 0) {
    die("\nMigration $option failed\n");
}

echo "\nMigrations run successfully!\n";
?>