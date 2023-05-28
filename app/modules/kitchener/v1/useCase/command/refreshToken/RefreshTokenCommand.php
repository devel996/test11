<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\refreshToken;

use app\interfaces\command\CommandInterface;

class RefreshTokenCommand implements CommandInterface
{
    private string $accessToken;
    private string $refreshToken;
    private string $ip;

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }
}