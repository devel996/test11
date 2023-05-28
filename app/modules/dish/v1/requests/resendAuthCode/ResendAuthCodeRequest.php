<?php

declare(strict_types=1);

namespace app\modules\auth\v2\requests\resendAuthCode;

use app\interfaces\command\CommandableInterface;
use app\modules\auth\v2\useCase\command\resendAuthCode\ResendAuthCodeCommand;
use yii\base\Model;

class ResendAuthCodeRequest extends Model implements CommandableInterface
{
    private ?string $email;

    /**
     * @return list<array>
     */
    public function rules(): array
    {
        return [
            [['email'], 'required'],
            [['email'], 'string'],
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

    public function getCommand(): ResendAuthCodeCommand
    {
        $command = new ResendAuthCodeCommand();

        $command->setEmail((string)$this->getEmail());

        return $command;
    }
}