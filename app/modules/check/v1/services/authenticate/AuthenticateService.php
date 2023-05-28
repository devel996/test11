<?php

declare(strict_types=1);

namespace app\modules\auth\v2\services\authenticate;

use app\components\JWTComponent;
use app\enums\ErrorCategory;
use app\models\User;
use app\modules\auth\models\AccessToken;
use app\modules\auth\v2\domain\enums\role\RoleEnum;
use app\modules\auth\v2\domain\valueObjects\common\TokenValueObject;
use app\modules\auth\v2\domain\valueObjects\response\AuthenticateResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\AuthCodeValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\rbac\ManagerInterface;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

class AuthenticateService
{
    private string $accessToken;
    private string $refreshToken;
    private User $user;

    public function __construct(EmailValueObject $email, AuthCodeValueObject $authCode)
    {
        /** @var User $user */
        $user = yiiUser()->identity;

        $accessToken = $user->getLastAttemptAccessToken();

        if ($accessToken === null || $authCode->getValue() !== $accessToken->auth_code) {
            throw new BadRequestHttpException('Incorrect Auth Code');
        }

        if ($email->getValue() !== $user->email) {
            throw new BadRequestHttpException('Incorrect Email');
        }

        $this->user = $user;
    }

    public function run(): AuthenticateResponseValueObject
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');
        $transaction = yiiWebApp()->db->beginTransaction();

        try {
            $this->accessToken = $jwt->generateAccessToken($this->user);
            $this->refreshToken = $jwt->generateRefreshToken($this->user);
            $this->setToken();
            $this->removeUser2faRole();
        } catch (HttpException $e) {
            $transaction->rollBack();

            throw $e;
        }

        $transaction->commit();

        // Access token value object
        $accessTokenValueObject = new TokenValueObject();
        $accessTokenValueObject->setToken($this->accessToken);
        $accessTokenValueObject->setExpiresAt($jwt->getExpiresAtFromToken($this->accessToken));

        // Refresh token value object
        $refreshTokenValueObject = new TokenValueObject();
        $refreshTokenValueObject->setToken($this->refreshToken);
        $refreshTokenValueObject->setExpiresAt($jwt->getExpiresAtFromToken($this->refreshToken));

        // Authenticate response value object
        $responseValueObject = new AuthenticateResponseValueObject();
        $responseValueObject->setAccessToken($accessTokenValueObject);
        $responseValueObject->setRefreshToken($refreshTokenValueObject);

        return $responseValueObject;
    }

    private function setToken(): void
    {
        if (($token = AccessToken::getLastNotUsedTokenByUserId($this->user->id)) === null) {
            $token = new AccessToken();
        }

        $token->scenario = AccessToken::SCENARIO_AUTHENTICATE;
        $token->user__id = $this->user->id;
        $token->access_token = $this->accessToken;
        $token->refresh_token = $this->refreshToken;
        $token->ip = (string)Yii::$app->request->userIP;
        $token->updated_at = new Expression('NOW()');

        if (!$token->save()) {
            throw new ServerErrorHttpException(ErrorCategory::DEFAULT_ERROR_RESPONSE_MESSAGE->value);
        }
    }

    private function removeUser2faRole(): void
    {
        /** @var ManagerInterface $authManager */
        $authManager = Yii::$app->authManager;

        if ($authManager === null) {
            throw new ServerErrorHttpException('Invalid component');
        }


        if ($this->user->has2faRole()) {
            $role = $authManager->getRole(RoleEnum::ROLE_NEW_LOGIN_ATTEMPT->value);

            if ($role === null) {
                throw new Exception('Could not get role');
            }

            $authManager->revoke($role, $this->user->id);
        }
    }
}
