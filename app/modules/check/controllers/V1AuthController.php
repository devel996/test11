<?php

declare(strict_types=1);

namespace app\modules\dish\controllers;

use app\modules\auth\authenticator\OAuth2SecondStageAuthenticator;
use app\modules\auth\v2\actions\AuthenticateAction;
use app\modules\auth\v2\actions\LoginAction;
use app\modules\auth\v2\actions\RefreshTokenAction;
use app\modules\auth\v2\actions\ResendAuthCodeAction;
use app\modules\auth\v2\actions\ResetPasswordAction;
use app\modules\auth\v2\domain\enums\role\RoleEnum;
use yii\filters\AccessControl;
use yii\filters\RateLimiter;
use yii\rest\Controller;

/**
 * Default controller for the `auth` module
 */
class V1AuthController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    public function actions(): array
    {
        return [
            'login' => [
                'class' => LoginAction::class
            ],
            'authenticate' => [
                'class' => AuthenticateAction::class
            ],
            'refresh-token' => [
                'class' => RefreshTokenAction::class
            ],
            'resend-auth-code' => [
                'class' => ResendAuthCodeAction::class
            ],
            'reset-password' => [
                'class' => ResetPasswordAction::class
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
        ];

        $behaviors['authenticator'] = [
            'class' => OAuth2SecondStageAuthenticator::class,
            'except' => ['login', 'refresh-token', 'reset-password'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['login', 'refresh-token', 'authenticate'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login', 'refresh-token', 'reset-password'],
                    'roles' => ['?'],
                ],
                [
                    'allow' => true,
                    'actions' => ['authenticate', 'resend-auth-code'],
                    'roles' => [RoleEnum::ROLE_NEW_LOGIN_ATTEMPT->value],
                ]
            ],
        ];

        return $behaviors;
    }

    /**
     * @return array<string, mixed>
     */
    protected function verbs(): array
    {
        return [
            'login' => ['POST'],
            'authenticate' => ['POST'],
            'refresh-token' => ['POST'],
            'resend-auth-code' => ['POST'],
            'reset-password' => ['POST'],
        ];
    }
}
