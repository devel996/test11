<?php

declare(strict_types=1);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require __DIR__ . '/../functions.php';

$webConfig = require __DIR__ . '/web.php';

$db = require __DIR__ . '/db_test.php';
$queue = require __DIR__ . '/queue.php';
$mailer = require __DIR__ . '/mailer.php';
$urlManager = $webConfig['components']['urlManager'];
$modules = $webConfig['modules'];
$components = $webConfig['components'];

$components['db'] = $db;

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'components' => $components,
    'modules' => $modules,
];


return $config;
