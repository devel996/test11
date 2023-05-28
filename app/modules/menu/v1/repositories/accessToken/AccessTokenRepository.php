<?php

declare(strict_types=1);

namespace app\modules\auth\v2\repositories\accessToken;

use app\modules\auth\v2\domain\entities\accessToken\RefreshAccessToken;
use app\modules\auth\v2\domain\valueObjects\validation\IpValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use app\traits\SingletonTrait;

class AccessTokenRepository
{
    use SingletonTrait;

    public function getRefreshAccessTokenByTokensAndIp(
        JwtTokenValueObject $accessToken,
        JwtTokenValueObject $refreshToken,
        IpValueObject $ip
    ): ?RefreshAccessToken
    {
        /** @var RefreshAccessToken|null $model */
        $model = RefreshAccessToken::findOne(
            [
                'access_token' => $accessToken->getValue(),
                'refresh_token' => $refreshToken->getValue(),
                'ip' => $ip->getValue()
            ]
        );

        return $model;
    }
}