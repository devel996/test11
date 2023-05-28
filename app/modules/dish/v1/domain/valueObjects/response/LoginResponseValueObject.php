<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\response;

use app\interfaces\response\ResponseValueObjectInterface;
use app\modules\auth\v2\domain\valueObjects\common\TokenValueObject;
use app\traits\ResponseCodeTrait;

final class LoginResponseValueObject implements ResponseValueObjectInterface
{
    use ResponseCodeTrait;

    private string $email;
    private bool $isTwoFaActive;
    private TokenValueObject $accessToken;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function isTwoFaActive(): bool
    {
        return $this->isTwoFaActive;
    }

    public function setIsTwoFaActive(bool $isTwoFaActive): void
    {
        $this->isTwoFaActive = $isTwoFaActive;
    }

    public function getAccessToken(): TokenValueObject
    {
        return $this->accessToken;
    }

    public function setAccessToken(TokenValueObject $accessToken): void
    {
        $this->accessToken = $accessToken;
    }
}