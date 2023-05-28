<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\resetPassword\firstStage;

use app\interfaces\handler\CommandHandlerInterface;
use app\modules\auth\v2\domain\valueObjects\response\ResetPasswordResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use app\modules\auth\v2\services\resetPassword\ResetPasswordFirstStageService;

class ResetPasswordHandler implements CommandHandlerInterface
{
    private ResetPasswordCommand $command;

    public function __construct(ResetPasswordCommand $command)
    {
        $this->command = $command;
    }

    public function getResponseData(): ResetPasswordResponseValueObject
    {
        $resetPasswordService = new ResetPasswordFirstStageService(
            new EmailValueObject($this->command->getEmail())
        );

        return $resetPasswordService->run();
    }
}