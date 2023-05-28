<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\response;

use app\interfaces\response\ResponseValueObjectInterface;
use app\modules\auth\v2\domain\valueObjects\common\TokenValueObject;
use app\traits\ResponseCodeTrait;

final class AuthenticateResponseValueObject implements ResponseValueObjectInterface
{
    use ResponseCodeTrait;

    private TokenValueObject $accessToken;
    private TokenValueObject $refreshToken;

    public function getAccessToken(): TokenValueObject
    {
        return $this->accessToken;
    }

    public function setAccessToken(TokenValueObject $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): TokenValueObject
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(TokenValueObject $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }
}