<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\resetPassword\secondStage;

use app\interfaces\handler\CommandHandlerInterface;
use app\modules\auth\v2\domain\valueObjects\response\ResetPasswordResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use app\modules\auth\v2\services\resetPassword\ResetPasswordSecondStageService;

class ResetPasswordHandler implements CommandHandlerInterface
{
    private ResetPasswordCommand $command;

    public function __construct(ResetPasswordCommand $command)
    {
        $this->command = $command;
    }

    public function getResponseData(): ResetPasswordResponseValueObject
    {
        $resetPasswordService = new ResetPasswordSecondStageService(
            new JwtTokenValueObject($this->command->getUuid())
        );

        return $resetPasswordService->run();
    }
}