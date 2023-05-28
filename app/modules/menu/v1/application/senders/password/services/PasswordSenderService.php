<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\password\services;

use app\models\AbstractUser;
use app\modules\auth\v2\application\senders\password\interfaces\PasswordSenderStrategyInterface;
use app\modules\auth\v2\domain\valueObjects\validation\PasswordValueObject;

class PasswordSenderService
{
    public function send(
        PasswordSenderStrategyInterface $strategy,
        AbstractUser $user,
        PasswordValueObject $password
    ): void
    {
        $strategy->send($user, $password);
    }
}
