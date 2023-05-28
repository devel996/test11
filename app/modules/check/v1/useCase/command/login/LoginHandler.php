<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\login;

use app\interfaces\handler\CommandHandlerInterface;
use app\modules\auth\v2\domain\valueObjects\response\AuthenticateResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\response\LoginResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\PasswordValueObject;
use app\modules\auth\v2\services\login\LoginService;

class LoginHandler implements CommandHandlerInterface
{
    private LoginCommand $command;

    public function __construct(LoginCommand $command)
    {
        $this->command = $command;
    }

    public function getResponseData(): LoginResponseValueObject|AuthenticateResponseValueObject
    {
        $loginService = new LoginService(
            new EmailValueObject($this->command->getEmail()),
            new PasswordValueObject($this->command->getPassword())
        );

        return $loginService->run();
    }
}