<?php

class Config
{
    function __construct($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("The .env file does not exist at {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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

}

$config = new Config(__DIR__ . '/.env');