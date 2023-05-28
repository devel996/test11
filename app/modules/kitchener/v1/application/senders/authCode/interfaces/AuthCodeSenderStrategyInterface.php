<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\authCode\interfaces;

use app\models\AbstractUser;
use app\modules\auth\v2\domain\entities\accessToken\AbstractAccessToken;

interface AuthCodeSenderStrategyInterface
{
    public function send(AbstractUser $user, AbstractAccessToken $token): void;
}
