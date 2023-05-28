<?php

$host = env('MYSQL_HOST', 'app_mysql');
$db = env('MYSQL_DATABASE', 'app');
$user = env('MYSQL_USER', 'root');
$password = env('MYSQL_USER_PASSWORD', 'secret');

/** @phpstan-ignore-next-line */
return [
    'class'        => 'yii\db\Connection',
    'dsn'          => 'mysql:host=' . $host . ';dbname=' . $db,
    'username'     => $user,
    'password'     => $password,
    'charset'      => 'utf8'
];
