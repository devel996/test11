<?php

declare(strict_types=1);

use app\models\User;
use app\modules\auth\AuthModule;
use yii\rbac\DbManager;
use app\components\JWTComponent;
use yii\redis\Cache;
use yii\web\JsonParser;
use yii\web\Response;

$db = require __DIR__ . '/db.php';
$queue = require __DIR__ . '/queue.php';
$mailer = require __DIR__ . '/mailer.php';
$log = require __DIR__ . '/log.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'components' => [
        'response' => [
            'format' => Response::FORMAT_JSON,
        ],
        'request' => [
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'enableCsrfCookie' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => env('REDIS_HOST', 'localhost'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DATABASE', '0'),
        ],
        'cache' => [
            'class' => Cache::class,
            'redis' => [
                'hostname' => env('REDIS_HOST', 'localhost'),
                'port' => env('REDIS_PORT', '6379'),
                'database' => env('REDIS_DATABASE', '0'),
            ],
        ],
        'jwt' => [
            'class' => JWTComponent::class
        ],
        'user' => [
            'identityClass' => User::class,
            'enableSession' => false,
        ],
        'authManager' => [
            'class' => DbManager::class,
        ],
       'errorHandler' => [
           'class' => 'app\components\ErrorHandler',
       ],
        'queue' => $queue,
        'mailer' => $mailer,
        'log' => $log,
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST api/v2/auth/<action:(login|authenticate|refresh-token|resend-auth-code|reset-password)>' => 'auth/v2-auth/<action>',
            ],
        ],
    ],
    'modules' => [
        'auth' => [
            'class' => AuthModule::class,
        ]
    ]
];
return $config;
