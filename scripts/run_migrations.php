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
        exec("php $file", $output, $code);

        if ($code !== 0) {
            die("Migration $file failed\n");
        }
    }
} else {
    exec("php $option $output, $code");

    if ($code !== 0) {
        die("Migration $file failed\n");
    }
}

echo "Migrations run successfully!\n";
?>