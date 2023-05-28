<?php

declare(strict_types=1);

namespace app\modules\auth\v2\services\resetPassword;

use app\components\JWTComponent;
use app\enums\HttpCode;
use app\modules\auth\v2\application\senders\uuid\strategies\EmailUuidSenderStrategy;
use app\modules\auth\v2\application\senders\uuid\services\UuidSenderService;
use app\modules\auth\v2\domain\proxies\User;
use app\modules\auth\v2\domain\valueObjects\response\ResetPasswordResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\EmailValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use app\modules\auth\v2\repositories\user\UserRepository;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

class ResetPasswordFirstStageService
{
    private User $user;

    public function __construct(EmailValueObject $email)
    {
        $user = UserRepository::getInstance()->getByEmail($email);

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        $this->user = $user;
    }

    public function run(): ResetPasswordResponseValueObject
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        $token = $jwt->generateUUIDToken($this->user);

        $this->user->uuid = $token;

        if (!$this->user->save()) {
            throw new Exception('Database error');
        }

        $uuidService = new UuidSenderService();
        $uuidService->send(
            new EmailUuidSenderStrategy(),
            $this->user,
            new JwtTokenValueObject($token)
        );

        $responseValueObject = new ResetPasswordResponseValueObject();
        $responseValueObject->setResponseCode(HttpCode::ACCEPTED->value);
        $responseValueObject->setEmail($this->user->email);

        return $responseValueObject;
    }
}
