<?php

declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $name, string $defaultValue = ''): string
    {
        $val = $_ENV[$name] ?? $defaultValue;

        return (string)$val;
    }
}