<?php

declare(strict_types=1);

namespace app\modules\auth\v2\application\senders\password\interfaces;

use app\models\AbstractUser;
use app\modules\auth\v2\domain\valueObjects\validation\PasswordValueObject;

interface PasswordSenderStrategyInterface
{
    public function send(AbstractUser $user, PasswordValueObject $password): void;
}
