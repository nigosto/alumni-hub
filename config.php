<?php
function load_config($file_path) {
    if (!file_exists($file_path)) {
        throw new Exception("The .env file does not exist at $file_path");
    }

    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "'\"");

        $_ENV[$key] = $value;
    }
}
?>