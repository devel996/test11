<?php

declare(strict_types=1);

namespace app\modules\auth\v2\actions;

use app\modules\auth\v2\requests\authenticate\AuthenticateRequest;
use app\modules\auth\v2\requests\refreshToken\RefreshTokenRequest;
use app\modules\auth\v2\useCase\command\authenticate\AuthenticateHandler;
use app\modules\auth\v2\useCase\command\refreshToken\RefreshTokenHandler;
use OpenApi\Attributes as OA;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class RefreshTokenAction extends Action
{
    #[OA\Post(
        path: '/api/v2/refresh-token',
        operationId: "post-refresh-token",
        summary: 'Refresh token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["access_token", "refresh_token"],
                properties: [
                    new OA\Property(
                        property: "access_token",
                        type: "string",
                        example: "aevrvsrvdtbvuhacbaybuyae.vravrsubvyusbryvusr.vsrvsrvrsvsr"
                    ),
                    new OA\Property(
                        property: "refresh_token",
                        type: "string",
                        example: "aevrvsrvdtbvuhacbaybuyae.vravrsubvyusbryvusr.vsrvsrvrsvsr"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tokens refresh',
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
        $request = new RefreshTokenRequest();

        $request->setAttributes([
            'accessToken' => strval(Yii::$app->request->getBodyParam('access_token')),
            'refreshToken' => strval(Yii::$app->request->getBodyParam('refresh_token')),
            'ip' => strval(Yii::$app->request->userIP)
        ]);

        if (!$request->validate()) {
            throw new BadRequestHttpException('Something went wrong!');
        }

        $responseValueObject = (new RefreshTokenHandler($request->getCommand()))->getResponseData();

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