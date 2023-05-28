<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\common;

use DateTimeImmutable;

final class TokenValueObject
{
    private string $token;
    private ?DateTimeImmutable $expiresAt;

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}