<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\resendAuthCode;

use app\interfaces\handler\CommandHandlerInterface;
use app\modules\auth\v2\domain\valueObjects\response\LoginResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use app\modules\auth\v2\services\resendAuthCode\ResendAuthCodeService;

class ResendAuthCodeHandler implements CommandHandlerInterface
{
    private ResendAuthCodeCommand $command;

    public function __construct(ResendAuthCodeCommand $command)
    {
        $this->command = $command;
    }

    public function getResponseData(): LoginResponseValueObject
    {
        $resendAuthCodeService = new ResendAuthCodeService(
            new EmailValueObject($this->command->getEmail())
        );

        return $resendAuthCodeService->run();
    }
}