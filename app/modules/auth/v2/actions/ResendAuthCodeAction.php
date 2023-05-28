<?php

declare(strict_types=1);

namespace app\modules\auth\v2\actions;

use app\exceptions\ValidationException;
use app\modules\auth\v2\requests\resendAuthCode\ResendAuthCodeRequest;
use app\modules\auth\v2\useCase\command\resendAuthCode\ResendAuthCodeHandler;
use OpenApi\Attributes as OA;
use Yii;
use yii\base\Action;
use yii\web\Response;

class ResendAuthCodeAction extends Action
{
    #[OA\Post(
        path: '/api/v2/resend-auth-code',
        operationId: "post-resend-auth-code",
        summary: 'Resend auth code',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(
                        property: "email",
                        ref: "#/components/schemas/EmailObject"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: 'Resend auth code successfully',
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
        $request = new ResendAuthCodeRequest();

        $request->setAttributes(
            [
                'email' => strval(Yii::$app->request->getBodyParam('email'))
            ]
        );

        if (!$request->validate()) {
            throw new ValidationException('email', 'Incorrect value');
        }

        $responseValueObject = (new ResendAuthCodeHandler($request->getCommand()))->getResponseData();

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
}