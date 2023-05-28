<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\uuid\interfaces;

use app\models\AbstractUser;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;

interface UuidSenderStrategyInterface
{
    public function send(AbstractUser $user, JwtTokenValueObject $uuid): void;
}
