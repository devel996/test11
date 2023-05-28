<?php

declare(strict_types=1);

namespace app\modules\auth\v2\services\refreshToken;

use app\components\JWTComponent;
use app\exceptions\ValidationException;
use app\modules\auth\v2\domain\entities\accessToken\RefreshAccessToken;
use app\modules\auth\v2\domain\valueObjects\common\TokenValueObject;
use app\modules\auth\v2\domain\valueObjects\response\AuthenticateResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\IpValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use app\modules\auth\v2\repositories\accessToken\AccessTokenRepository;
use yii\db\Exception;
use yii\db\Expression;
use yii\web\UnauthorizedHttpException;

class RefreshTokenService
{
    private RefreshAccessToken $token;

    public function __construct(
        JwtTokenValueObject $accessToken,
        JwtTokenValueObject $refreshToken,
        IpValueObject $ip
    )
    {
        /** @var RefreshAccessToken $token */
        $token = AccessTokenRepository::getInstance()
            ->getRefreshAccessTokenByTokensAndIp(
                $accessToken,
                $refreshToken,
                $ip
            );

        if (!$this->isRefreshTokenValid($token)) {
            throw new ValidationException(
                'refresh_token',
                'Invalid user token'
            );
        }

        $this->token = $token;
    }

    public function run(): AuthenticateResponseValueObject
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        $this->refreshTheAccessToken();

        // Access token value object
        $accessTokenValueObject = new TokenValueObject();
        $accessTokenValueObject->setToken($this->token->access_token);
        $accessTokenValueObject->setExpiresAt($jwt->getExpiresAtFromToken($this->token->access_token));

        // Refresh token value object
        $refreshTokenValueObject = new TokenValueObject();
        $refreshTokenValueObject->setToken($this->token->refresh_token);
        $refreshTokenValueObject->setExpiresAt($jwt->getExpiresAtFromToken($this->token->refresh_token));

        // Authenticate response value object
        $responseValueObject = new AuthenticateResponseValueObject();
        $responseValueObject->setAccessToken($accessTokenValueObject);
        $responseValueObject->setRefreshToken($refreshTokenValueObject);

        return $responseValueObject;
    }

    private function refreshTheAccessToken(): void
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        $this->token->access_token = $jwt->generateAccessToken($this->token->user);
        $this->token->refresh_token = $jwt->generateRefreshToken($this->token->user);
        $this->token->updated_at = new Expression('NOW()');

        if (!$this->token->validate()) {
            throw new UnauthorizedHttpException('Unauthorized');
        }

        if (!$this->token->save()) {
            throw new Exception('Something went wrong');
        }
    }

    private function isRefreshTokenValid(?RefreshAccessToken $token): bool
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        if (
            $token === null ||
            !$jwt->isValid($token->refresh_token) ||
            ($userId = $jwt->getUserIdFromToken($token->access_token)) === null
        ) {
            return false;
        }

        return $token->user__id === $userId;
    }
}
