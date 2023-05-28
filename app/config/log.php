<?php

use yii\log\FileTarget;

return [
    'traceLevel' => 3,
    'targets' => [
        'class' => FileTarget::class,
        'levels' => ['error'],
        'categories' => ['mainError'],
        'logFile' => '@runtime/main-logs/errors.log'
    ]
];
