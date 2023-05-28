<?php

declare(strict_types=1);

namespace app\modules\auth\v2\actions;

use app\modules\auth\v2\requests\authenticate\AuthenticateRequest;
use app\modules\auth\v2\useCase\command\authenticate\AuthenticateHandler;
use OpenApi\Attributes as OA;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class AuthenticateAction extends Action
{
    #[OA\Post(
        path: '/api/v2/authenticate',
        operationId: "post-authenticate",
        summary: 'Authenticate Oauth2 second stage',
        security: [
            [
                "BearerAuthObject" => []
            ]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "auth_code"],
                properties: [
                    new OA\Property(
                        property: "email",
                        ref: "#/components/schemas/EmailObject"
                    ),
                    new OA\Property(
                        property: "auth_code",
                        type: "integer",
                        maximum: 6,
                        minimum: 6,
                        example: "111111"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated successfully',
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
        $request = new AuthenticateRequest();

        $request->setAttributes([
            'email' => strval(Yii::$app->request->getBodyParam('email')),
            'authCode' => intval(Yii::$app->request->getBodyParam('auth_code'))
        ]);

        if (!$request->validate()) {
            throw new BadRequestHttpException('Email or auth code are incorrect');
        }

        $responseValueObject = (new AuthenticateHandler($request->getCommand()))->getResponseData();

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