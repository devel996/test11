<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\uuid\strategies;

use app\models\AbstractUser;
use app\modules\auth\v2\application\senders\uuid\interfaces\UuidSenderStrategyInterface;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use yii\web\ServerErrorHttpException;

class EmailUuidSenderStrategy implements UuidSenderStrategyInterface
{
    public function send(AbstractUser $user, JwtTokenValueObject $uuid): void
    {
        $link = env(
                'FRONTEND_HOST',
                'http://front.local'
            ) . '/auth/sign-in?uuid=' . $uuid->getValue();

        $wasEmailSent = yiiWebApp()->mailer->compose(
            'uuid',
            [
            'uuid' => '<a target="_blank" href="' . $link . '">' . $link . '</a>'
            ]
        )
            ->setFrom((string)env('SENDER_EMAIL', 'noreply@example.com'))
            ->setTo((string)$user->email)
            ->setSubject('Password reset first stage')
            ->setTextBody($link)
            ->send();

        if (!$wasEmailSent) {
            throw new ServerErrorHttpException('Email was not sent');
        }
    }
}
