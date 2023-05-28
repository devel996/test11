<?php

declare(strict_types=1);

namespace app\modules\auth\v2\actions;

use app\modules\auth\v2\domain\valueObjects\response\LoginResponseValueObject;
use app\modules\auth\v2\requests\login\LoginRequest;
use app\modules\auth\v2\useCase\command\login\LoginHandler;
use OpenApi\Attributes as OA;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class LoginAction extends Action
{
    #[OA\Post(
        path: '/api/v2/login',
        operationId: "post-login",
        summary: 'Login Oauth2 first stage',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(
                        property: "email",
                        ref: "#/components/schemas/EmailObject"
                    ),
                    new OA\Property(
                        property: "password",
                        type: "string",
                        minimum: 6,
                        example: "111111"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'If is not 2fa user then user should be authenticated',
                content: new OA\JsonContent(
                    required: ["access_token", "refresh_token"],
                    properties: [
                        new OA\Property(
                            property: "access_token",
                            ref: "#/components/schemas/JwtTokenObject",
                            description: 'JWT access token'
                        ),
                        new OA\Property(
                            property: "refresh_token",
                            ref: "#/components/schemas/JwtTokenObject",
                            description: 'JWT refresh token'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 202,
                description: 'If user has 2fa then send auth code',
                content: new OA\JsonContent(
                    required: ["email", "is_two_fa_active", "access_token"],
                    properties: [
                        new OA\Property(
                            property: "email",
                            ref: "#/components/schemas/EmailObject"
                        ),
                        new OA\Property(
                            property: "is_two_fa_active",
                            type: "boolean",
                            enum: ["true", "false"],
                            example: true
                        ),
                        new OA\Property(
                            property: "access_token",
                            ref: "#/components/schemas/JwtTokenObject",
                            description: 'JWT access token'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad Request",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/BadRequestObject"
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/UnauthorizedObject"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Server Error",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/ServerErrorObject"
                )
            )
        ]
    )]
    public function run(): Response
    {
        $request = new LoginRequest();

        $request->setAttributes([
            'email' => Yii::$app->request->getBodyParam('email'),
            'password' => Yii::$app->request->getBodyParam('password')
        ]);

        if (!$request->validate()) {
            throw new BadRequestHttpException('Email or Password are incorrect');
        }

        $responseValueObject = (new LoginHandler($request->getCommand()))->getResponseData();

        if ($responseValueObject instanceof LoginResponseValueObject) {
            return new Response(
                [
                    'statusCode' => $responseValueObject->getResponseCode(),
                    'format' => Response::FORMAT_JSON,
                    'data' => [
                        'email' => $responseValueObject->getEmail(),
                        'is_two_fa_active' => $responseValueObject->isTwoFaActive(),
                        'access_token' => [
                            'token' => $responseValueObject->getAccessToken()->getToken(),
                            'expiresAt' => $responseValueObject->getAccessToken()->getExpiresAt()
                        ]
                    ]
                ]
            );
        }

        return new Response(
            [
                'statusCode' => $responseValueObject->getResponseCode(),
                'format' => Response::FORMAT_JSON,
                'data' => [
                    'access_token' => [
                        'token' => $responseValueObject->getAccessToken()->getToken(),
                        'expiresAt' => $responseValueObject->getAccessToken()->getExpiresAt()
                    ],
                    'refresh_token' => [
                        'token' => $responseValueObject->getRefreshToken()->getToken(),
                        'expiresAt' => $responseValueObject->getRefreshToken()->getExpiresAt()
                    ]
                ]
            ]
        );
    }
}