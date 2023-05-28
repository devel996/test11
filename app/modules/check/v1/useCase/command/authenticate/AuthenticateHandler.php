<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\authenticate;

use app\interfaces\handler\CommandHandlerInterface;
use app\modules\auth\v2\domain\valueObjects\response\AuthenticateResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\AuthCodeValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use app\modules\auth\v2\services\authenticate\AuthenticateService;

class AuthenticateHandler implements CommandHandlerInterface
{
    private AuthenticateCommand $command;

    public function __construct(AuthenticateCommand $command)
    {
        $this->command = $command;
    }

    public function getResponseData(): AuthenticateResponseValueObject
    {
        $authService = new AuthenticateService(
            new EmailValueObject($this->command->getEmail()),
            new AuthCodeValueObject($this->command->getAuthCode())
        );

        return $authService->run();
    }
}