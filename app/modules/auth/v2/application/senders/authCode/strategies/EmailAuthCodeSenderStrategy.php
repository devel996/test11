<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\authCode\strategies;

use app\modules\auth\v2\application\senders\authCode\interfaces\AuthCodeSenderStrategyInterface;
use app\modules\auth\v2\domain\entities\accessToken\AbstractAccessToken;
use app\models\AbstractUser;
use yii\web\ServerErrorHttpException;

class EmailAuthCodeSenderStrategy implements AuthCodeSenderStrategyInterface
{
    public function send(AbstractUser $user, AbstractAccessToken $token): void
    {
        $wasEmailSent = yiiWebApp()->mailer->compose(
            'new-auth-code',
            [
                'token' => $token->auth_code
            ]
        )
            ->setFrom((string)env('SENDER_EMAIL', 'noreply@example.com'))
            ->setTo((string)$user->email)
            ->setSubject('New Authentication Code')
            ->setTextBody((string)$token->auth_code)
            ->send();

        if (!$wasEmailSent) {
            throw new ServerErrorHttpException('Email was not sent');
        }
    }
}
