<?php

declare(strict_types=1);

namespace app\modules\auth\v2\requests\refreshToken;

use app\interfaces\command\CommandableInterface;
use app\modules\auth\v2\useCase\command\refreshToken\RefreshTokenCommand;
use yii\base\Model;

class RefreshTokenRequest extends Model implements CommandableInterface
{
    private ?string $accessToken;
    private ?string $refreshToken;
    private ?string $ip;

    /**
     * @return list<array>
     */
    public function rules(): array
    {
        return [
            [['refreshToken', 'accessToken', 'ip'], 'required'],
            [['refreshToken', 'accessToken', 'ip'], 'string']
        ];
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
    }

    public function getCommand(): RefreshTokenCommand
    {
        $command = new RefreshTokenCommand();

        $command->setAccessToken((string)$this->getAccessToken());
        $command->setRefreshToken((string)$this->getRefreshToken());
        $command->setIp((string)$this->getIp());

        return $command;
    }
}