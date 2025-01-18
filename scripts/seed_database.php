<?php
if ($argc > 3) {
    throw new Exception("Invalid number of arguments");
}

$option = $argv[1];
$scripts_locations = __DIR__ . "/../database/";

$output = null;
$code = 0;
$option = $scripts_locations . $option;

$ext = strtolower(pathinfo($option, PATHINFO_EXTENSION));
if (empty($ext)) {
    if ($option[strlen($option)] === "/" || $option[strlen($option)] === "/") {
        $option = substr($option, 0, -1);
    }

    $option .= "/seed.php";
}

exec("php $option", $output, $code);

if ($code !== 0) {
    die("\Seeding $option failed\n");
}

echo "Database seeded successfully!\n";
?>