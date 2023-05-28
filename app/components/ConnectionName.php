<?php

declare(strict_types=1);

namespace app\components;

abstract class ConnectionName
{
    public const DB = 'db';
    public const DB_TEST = 'dbTest';

    private static string $name = self::DB;

    public static function set(string $name): void
    {
        self::$name = $name;
    }

    public static function get(): string
    {
        return self::$name;
    }

    public static function getTestDatabaseName(): string
    {
        return env('MYSQL_TEST_DATABASE', 'app_test');
    }

    public static function getOriginalDatabaseName(): string
    {
        return env('MYSQL_DATABASE', 'app');
    }
}