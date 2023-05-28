<?php

declare(strict_types=1);

namespace app\modules\auth\v2\requests\login;

use app\interfaces\command\CommandableInterface;
use app\modules\auth\v2\useCase\command\login\LoginCommand;
use yii\base\Model;

class LoginRequest extends Model implements CommandableInterface
{
    private ?string $email;
    private ?string $password;

    /**
     * @return list<array>
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'string'],
        ];
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getCommand(): LoginCommand
    {
        $command = new LoginCommand();

        $command->setEmail((string)$this->getEmail());
        $command->setPassword((string)$this->getPassword());

        return $command;
    }
}