<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\uuid\services;

use app\models\AbstractUser;
use app\modules\auth\v2\application\senders\uuid\interfaces\UuidSenderStrategyInterface;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;

class UuidSenderService
{
    public function send(
        UuidSenderStrategyInterface $strategy,
        AbstractUser $user,
        JwtTokenValueObject $uuid
    ): void
    {
        $strategy->send($user, $uuid);
    }
}
