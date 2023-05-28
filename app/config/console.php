<?php

use yii\console\controllers\MigrateController;
use app\components\ConsoleDbManager;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . '/../functions.php';

$db = require __DIR__ . '/db.php';
$dbTest = require __DIR__ . '/db_test.php';
$queue = require __DIR__ . '/queue.php';
$mailer = require __DIR__ . '/mailer.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => ConsoleDbManager::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'queue' => $queue,
        'mailer' => $mailer,
        'db' => $db,
        'dbTest' => $dbTest,
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationPath' => [
                '@app/migrations',
                '@app/modules/auth/migrations',
            ],
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
            ],
        ],
    ]
];

return $config;
