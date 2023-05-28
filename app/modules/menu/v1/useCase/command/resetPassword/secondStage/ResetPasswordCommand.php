<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\resetPassword\secondStage;

use app\interfaces\command\CommandInterface;

class ResetPasswordCommand implements CommandInterface
{
    private string $uuid;

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }
}