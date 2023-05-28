<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\password\strategies;

use app\models\AbstractUser;
use app\modules\auth\v2\application\senders\password\interfaces\PasswordSenderStrategyInterface;
use app\modules\auth\v2\domain\valueObjects\validation\PasswordValueObject;
use yii\web\ServerErrorHttpException;

class EmailPasswordSenderStrategy implements PasswordSenderStrategyInterface
{
    public function send(AbstractUser $user, PasswordValueObject $password): void
    {
        $wasEmailSent = yiiWebApp()->mailer->compose(
            'password',
            [
            'password' => $password->getValue()
            ]
        )
            ->setFrom((string)env('SENDER_EMAIL', 'noreply@example.com'))
            ->setTo((string)$user->email)
            ->setSubject('Password reset second stage')
            ->setTextBody($password->getValue())
            ->send();

        if (!$wasEmailSent) {
            throw new ServerErrorHttpException('Email was not sent');
        }
    }
}
