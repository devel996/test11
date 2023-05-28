<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\authCode\services;

use app\models\AbstractUser;
use app\modules\auth\v2\application\senders\authCode\interfaces\AuthCodeSenderStrategyInterface;
use app\modules\auth\v2\domain\entities\accessToken\AbstractAccessToken;

class AuthCodeSenderService
{
    public function send(AuthCodeSenderStrategyInterface $strategy, AbstractUser $user, AbstractAccessToken $token): void
    {
        $strategy->send($user, $token);
    }
}
