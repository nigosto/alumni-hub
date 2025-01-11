<?php
if ($argc != 3) {
    throw new Exception("Invalid number of arguments");
}

$action = $argv[1];
$option = $argv[2];
$scripts_locations = __DIR__ . "/../database/migrations";

if ($action === "-m" || $action === "-migrate") {
    $scripts_locations .= "/*/migrate.php";
} else if ($action === "-r" || $action === "-rollback") {
    $scripts_locations .= "/*/rollback.php";    
} else {
    throw new Exception("Invalid action");
}

$output = null;
$code = 0;

if ($option === "-a" || $option === "-all") {
    foreach(glob($scripts_locations) as $file) {
        echo("php $file $output $code");
        exec("php $file", $output, $code);

        if ($code !== 0) {
            die("Migration $file failed\n");
        }
    }
} else {
    // $ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION));

    // if (empty($ext)) {

    // }

    exec("php $option", $output, $code);

    if ($code !== 0) {
        die("\nMigration $option failed\n");
    }
}

echo "\nMigrations run successfully!\n";
?>