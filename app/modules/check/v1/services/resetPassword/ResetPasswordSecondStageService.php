<?php

declare(strict_types=1);

namespace app\modules\auth\v2\services\resetPassword;

use app\enums\ErrorCategory;
use app\modules\auth\v2\application\senders\password\services\PasswordSenderService;
use app\modules\auth\v2\application\senders\password\strategies\EmailPasswordSenderStrategy;
use app\modules\auth\v2\domain\proxies\User;
use app\modules\auth\v2\domain\valueObjects\response\ResetPasswordResponseValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\JwtTokenValueObject;
use app\modules\auth\v2\domain\valueObjects\validation\PasswordValueObject;
use app\modules\auth\v2\repositories\user\UserRepository;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

class ResetPasswordSecondStageService
{
    private User $user;

    public function __construct(JwtTokenValueObject $uuid)
    {
        $user = UserRepository::getInstance()->getByUuid($uuid);

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        $this->user = $user;
    }

    public function run(): ResetPasswordResponseValueObject
    {
        $password = uniqid('pass');
        $hash = yiiWebApp()->getSecurity()->generatePasswordHash($password);

        $this->user->password = $hash;
        $this->user->uuid = null;

        if (!$this->user->save()) {
            throw new Exception('Database error');
        }

        $passwordService = new PasswordSenderService();
        $passwordService->send(
            new EmailPasswordSenderStrategy(),
            $this->user,
            new PasswordValueObject($password)
        );

        $responseValueObject = new ResetPasswordResponseValueObject();
        $responseValueObject->setEmail($this->user->email);

        return $responseValueObject;
    }
}
