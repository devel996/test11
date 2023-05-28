<?php

declare(strict_types=1);

namespace app\modules\auth\v2\services\login;

use app\components\JWTComponent;
use app\enums\HttpCode;
use app\modules\auth\v2\application\senders\authCode\services\AuthCodeSenderService;
use app\modules\auth\v2\application\senders\authCode\strategies\EmailAuthCodeSenderStrategy;
use app\modules\auth\v2\domain\entities\accessToken\AbstractAccessToken;
use app\modules\auth\v2\domain\entities\accessToken\AuthenticateAccessToken;
use app\modules\auth\v2\domain\entities\accessToken\LoginAccessToken;
use app\modules\auth\v2\domain\enums\role\RoleEnum;
use app\modules\auth\v2\domain\proxies\User;
use app\modules\auth\v2\domain\valueObjects\common\TokenValueObject;
use app\modules\auth\v2\domain\valueObjects\response\AuthenticateResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\response\LoginResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\PasswordValueObject;
use app\modules\auth\v2\repositories\user\UserRepository;
use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\rbac\ManagerInterface;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

class LoginService
{
    private AuthCodeSenderService $authCodeSenderService;
    private User $user;
    private int $authCode;

    public function __construct(EmailValueObject $email, PasswordValueObject $password)
    {
        $user = UserRepository::getInstance()->getByEmail($email);

        if ($user === null || !$user->hasValidPassword($password->getValue())) {
            throw new BadRequestHttpException('Invalid User');
        }

        $this->user = $user;
        $this->authCodeSenderService = new AuthCodeSenderService();
    }

    public function run(): LoginResponseValueObject|AuthenticateResponseValueObject
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        if (!$this->user->is_confirmed || !$this->user->is_active) {
            throw new BadRequestHttpException('Invalid User');
        }

        if (!$this->user->is_two_fa_active) {
            return $this->userLogin();
        }

        $transaction = yiiWebApp()->db->beginTransaction();

        try {
            $this->authCode = rand(100000, 999999); // Generate a random auth_code for user

            $token = $this->createNewLoginAttempt();

            $this->addUser2faRole(); // Add 2fa role to user

            // Send via email
            $this->authCodeSenderService->send(new EmailAuthCodeSenderStrategy(), $this->user, $token);
        } catch (HttpException $e) {
            $transaction->rollBack();

            throw $e;
        }

        $transaction->commit();

        // Set token value object
        $tokenValueObject = new TokenValueObject();
        $tokenValueObject->setToken($token->access_token);
        $tokenValueObject->setExpiresAt($jwt->getExpiresAtFromToken($token->access_token));

        // Set login response value object
        $responseValueObject = new LoginResponseValueObject();
        $responseValueObject->setResponseCode(HttpCode::ACCEPTED->value);
        $responseValueObject->setEmail($this->user->email);
        $responseValueObject->setIsTwoFaActive(boolval($this->user->is_two_fa_active));
        $responseValueObject->setAccessToken($tokenValueObject);

        return $responseValueObject;
    }

    private function createNewLoginAttempt(): AbstractAccessToken
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        // Don't create additional rows in mysql
        if (($token = LoginAccessToken::getLastNotUsedTokenByUserId($this->user->id)) === null) {
            $token = new LoginAccessToken();
        }

        $token->auth_code = $this->authCode;
        $token->user__id = $this->user->id;
        $token->ip = (string)Yii::$app->request->userIP;
        $token->access_token = $jwt->generateAccessToken($this->user);
        $token->updated_at = new Expression('NOW()');

        if (!$token->validate()) {
            throw new BadRequestHttpException('Invalid Access Token');
        }

        if (!$token->save()) {
            throw new Exception('Could not save access token');
        }

        return $token;
    }

    private function addUser2faRole(): void
    {
        /** @var ManagerInterface $authManager */
        $authManager = Yii::$app->authManager;

        if ($authManager === null) {
            throw new ServerErrorHttpException('Invalid component');
        }

        if (!$this->user->has2faRole()) {
            $role = $authManager->getRole(RoleEnum::ROLE_NEW_LOGIN_ATTEMPT->value);

            if ($role === null) {
                throw new Exception('Could not get role');
            }

            $authManager->assign($role, $this->user->id);
        }
    }

    private function userLogin(): AuthenticateResponseValueObject
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');
        $transaction = yiiWebApp()->db->beginTransaction();

        try {
            $accessToken = $jwt->generateAccessToken($this->user);
            $refreshToken = $jwt->generateRefreshToken($this->user);
            $this->setToken($accessToken, $refreshToken);
        } catch (HttpException $e) {
            $transaction->rollBack();

            throw $e;
        }

        $transaction->commit();

        // Access token value object
        $accessTokenValueObject = new TokenValueObject();
        $accessTokenValueObject->setToken($accessToken);
        $accessTokenValueObject->setExpiresAt($jwt->getExpiresAtFromToken($accessToken));

        // Refresh token value object
        $refreshTokenValueObject = new TokenValueObject();
        $refreshTokenValueObject->setToken($refreshToken);
        $refreshTokenValueObject->setExpiresAt($jwt->getExpiresAtFromToken($refreshToken));

        // Authenticate response value object
        $responseValueObject = new AuthenticateResponseValueObject();
        $responseValueObject->setAccessToken($accessTokenValueObject);
        $responseValueObject->setRefreshToken($refreshTokenValueObject);

        return $responseValueObject;
    }

    private function setToken(string $accessToken, string $refreshToken): void
    {
        if (($token = AuthenticateAccessToken::getLastNotUsedTokenByUserId($this->user->id)) === null) {
            $token = new AuthenticateAccessToken();
        }

        $token->user__id = $this->user->id;
        $token->access_token = $accessToken;
        $token->refresh_token = $refreshToken;
        $token->ip = (string)Yii::$app->request->userIP;
        $token->updated_at = new Expression('NOW()');

        if (!$token->save()) {
            throw new Exception('Access token did not saved');
        }
    }
}
