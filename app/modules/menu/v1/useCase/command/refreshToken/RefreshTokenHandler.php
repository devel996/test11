<?php

declare(strict_types=1);

namespace app\modules\auth\v2\useCase\command\refreshToken;

use app\interfaces\handler\CommandHandlerInterface;
use app\modules\auth\v2\domain\valueObjects\response\AuthenticateResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\IpValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use app\modules\auth\v2\services\refreshToken\RefreshTokenService;

class RefreshTokenHandler implements CommandHandlerInterface
{
    private RefreshTokenCommand $command;

    public function __construct(RefreshTokenCommand $command)
    {
        $this->command = $command;
    }

    public function getResponseData(): AuthenticateResponseValueObject
    {
        $refreshTokenService = new RefreshTokenService(
            new JwtTokenValueObject($this->command->getAccessToken(), 'access_token'),
            new JwtTokenValueObject($this->command->getRefreshToken(), 'refresh_token'),
            new IpValueObject($this->command->getIp(), 'ip')
        );

        return $refreshTokenService->run();
    }
}