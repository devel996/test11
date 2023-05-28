<?php

declare(strict_types=1);

namespace app\modules\auth\v2\repositories\user;

use app\modules\auth\v2\domain\proxies\User;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use app\traits\SingletonTrait;

class UserRepository
{
    use SingletonTrait;

    public function getByEmail(EmailValueObject $email): ?User
    {
        /** @var User|null $user */
        $user = User::findOne([
            'email' => $email->getValue(),
            'deleted_at' => null
        ]);

        return $user;
    }

    public function getByUuid(JwtTokenValueObject $uuid): ?User
    {
        /** @var User|null $user */
        $user = User::findOne([
            'uuid' => $uuid->getValue(),
            'deleted_at' => null
        ]);

        return $user;
    }
}