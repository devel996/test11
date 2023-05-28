<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\proxies;

use app\models\AbstractUser;

class User extends AbstractUser
{
    public const CLASS_DESCRIPTION = 'AuthProxyUser';
}