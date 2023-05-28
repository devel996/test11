<?php

declare(strict_types=1);

namespace app\modules\auth\v2\requests\authenticate;

use app\interfaces\command\CommandableInterface;
use app\modules\auth\v2\domain\proxies\User;
use app\modules\auth\v2\useCase\command\authenticate\AuthenticateCommand;
use yii\base\Model;

class AuthenticateRequest extends Model implements CommandableInterface
{
    private ?string $email;
    private ?int $authCode;

    /**
     * @return list<array>
     */
    public function rules(): array
    {
        return [
            [['email', 'authCode'], 'required'],
            [['email'], 'string'],
            [['email'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['email' => 'email']],
            [['authCode'], 'integer'],
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

    public function getAuthCode(): ?int
    {
        return $this->authCode;
    }

    public function setAuthCode(?int $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getCommand(): AuthenticateCommand
    {
        $command = new AuthenticateCommand();

        $command->setEmail((string)$this->getEmail());
        $command->setAuthCode((int)$this->getAuthCode());

        return $command;
    }
}