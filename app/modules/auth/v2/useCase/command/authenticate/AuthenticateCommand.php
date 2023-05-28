<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\authenticate;

use app\interfaces\command\CommandInterface;

class AuthenticateCommand implements CommandInterface
{
    private string $email;
    private int $authCode;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getAuthCode(): int
    {
        return $this->authCode;
    }

    public function setAuthCode(int $authCode): void
    {
        $this->authCode = $authCode;
    }
}