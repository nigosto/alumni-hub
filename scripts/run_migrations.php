<?php
if ($argc > 3) {
    throw new Exception("Invalid number of arguments");
}

$option = $argv[2];
$action = $argv[1];
$scripts_locations = __DIR__ . "/../database/migrations/";

$output = null;
$code = 0;

if ($option === "-a" || $option === "-all") {
    if ($action === "-m" || $action === "-migrate") {
        $scripts_locations .= "/*/migrate.php";
    } else if ($action === "-r" || $action === "-rollback") {
        $scripts_locations .= "/*/rollback.php";    
    } else {
        throw new Exception("Invalid action");
    }

    $files = glob($scripts_locations);
    $files = $action === "-r" ? array_reverse($files) : $files;
    foreach($files as $file) {
        $migration_file = $file;

        exec("php $migration_file", $output, $code);

        if ($code !== 0) {
            die("Migration $migration_file failed\n");
        }
    }

    echo "\nMigrations run successfully!\n";
    return;
}

$option = $scripts_locations . $option;

$ext = strtolower(pathinfo($option, PATHINFO_EXTENSION));
if (empty($ext)) {
    if ($option[strlen($option)] === "/" || $option[strlen($option)] === "/") {
        $option = substr($option, 0, -1);
    }

    if ($action === "-r" || $action === "-rollback") {
        $option .= "/rollback.php";
    }
    else if ($action === "-m" || $action === "-migrate") {
        $option .= "/migrate.php";
    }
}

exec("php $option", $output, $code);

if ($code !== 0) {
    die("\nMigration $option failed\n");
}

echo "\nMigrations run successfully!\n";
?>