<?php

declare(strict_types=1);

namespace app\modules\auth\v2\services\resendAuthCode;

use app\components\JWTComponent;
use app\enums\HttpCode;
use app\exceptions\ValidationException;
use app\models\User;
use app\modules\auth\v2\application\senders\authCode\strategies\EmailAuthCodeSenderStrategy;
use app\modules\auth\v2\application\senders\authCode\services\AuthCodeSenderService;
use app\modules\auth\v2\domain\entities\accessToken\LoginAccessToken;
use app\modules\auth\v2\domain\valueObjects\common\TokenValueObject;
use app\modules\auth\v2\domain\valueObjects\response\LoginResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\web\UnauthorizedHttpException;

class ResendAuthCodeService
{
    private AuthCodeSenderService $authCodeSenderService;
    private User $user;

    public function __construct(EmailValueObject $email)
    {
        /** @var User $user */
        $user = yiiUser()->identity;
        $this->authCodeSenderService = new AuthCodeSenderService();

        if ($user->email !== $email->getValue()) {
            throw new ValidationException(
                'email',
                'Invalid data'
            );
        }

        $this->user = $user;
    }

    public function run(): LoginResponseValueObject
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        $token = $this->createToken();

        $this->authCodeSenderService->send(
            new EmailAuthCodeSenderStrategy(),
            $this->user,
            $token
        );

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

    private function createToken(): LoginAccessToken
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        $authCode = rand(100000, 999999);

        $token = LoginAccessToken::getLastNotUsedTokenByUserId($this->user->id);

        if ($token === null) {
            $token = new LoginAccessToken();
        }

        $token->auth_code = $authCode;
        $token->ip = (string)Yii::$app->request->userIP;
        $token->user__id = $this->user->id;
        $token->access_token = $jwt->generateAccessToken($this->user);
        $token->updated_at = new Expression('NOW()');

        if (!$token->validate()) {
            throw new UnauthorizedHttpException('Invalid Token');
        }

        if (!$token->save()) {
            throw new Exception('Database error');
        }

        return $token;
    }
}
