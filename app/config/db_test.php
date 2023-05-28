<?php

$testHost = env('MYSQL_TEST_HOST', 'app_mysql_test');
$testDb = env('MYSQL_TEST_DATABASE', 'app_test');
$testUser = env('MYSQL_ROOT_USER', 'root');
$testPassword = env('MYSQL_USER_PASSWORD', 'secret');

return [
    'class'        => 'yii\db\Connection',
    'dsn'          => 'mysql:host=' . $testHost . ';dbname=' . $testDb,
    'username'     => $testUser,
    'password'     => $testPassword,
    'charset'      => 'utf8'
];
