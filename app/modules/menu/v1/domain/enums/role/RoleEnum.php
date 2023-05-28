<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\enums\role;

enum RoleEnum: string
{
    case ROLE_NEW_LOGIN_ATTEMPT = '2fa';
}