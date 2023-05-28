<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\login;

use app\interfaces\command\CommandInterface;

class LoginCommand implements CommandInterface
{
    private string $email;
    private string $password;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}