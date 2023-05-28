<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\resetPassword\firstStage;

use app\interfaces\command\CommandInterface;

class ResetPasswordCommand implements CommandInterface
{
    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}