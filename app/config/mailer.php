<?php

/** @phpstan-ignore-next-line */
return [
    'class' => \shaqman\mailer\queuemailer\Mailer::class,
    'queue' => 'queue',
    'syncMailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => false,
        'transport' => [
            'class' => getenv('MAILER_TRANSPORT_CLASS'),
            'host' => getenv('MAILER_HOST'),
            'port' => getenv('MAILER_PORT')
        ],
    ],
];