<?php

declare(strict_types=1);

namespace app\modules\auth\v2\actions;

use app\modules\auth\v2\useCase\command\resetPassword\firstStage\ResetPasswordCommand as FSC;
use app\modules\auth\v2\useCase\command\resetPassword\firstStage\ResetPasswordHandler as FSH;
use app\modules\auth\v2\useCase\command\resetPassword\secondStage\ResetPasswordCommand as SSC;
use app\modules\auth\v2\useCase\command\resetPassword\secondStage\ResetPasswordHandler as SSH;
use OpenApi\Attributes as OA;
use Yii;
use yii\base\Action;
use yii\web\Response;

class ResetPasswordAction extends Action
{
    #[OA\Post(
        path: '/api/v2/reset-password',
        operationId: "post-reset-password",
        summary: 'Reset password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "email",
                        ref: "#/components/schemas/EmailObject"
                    ),
                    new OA\Property(
                        property: "uuid",
                        type: "string",
                        example: "aevrvsrvdtbvuhacbaybuyae.vravrsubvyusbryvusr.vsrvsrvrsvsr"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reset password - send password to email',
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
            new OA\Response(
                response: 202,
                description: 'Reset password - send uuid to email',
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
        $uuid = Yii::$app->request->getBodyParam('uuid');

        if ($uuid === null) {
            $command = new FSC();
            $command->setEmail(strval(Yii::$app->request->getBodyParam('email')));
            $responseValueObject = (new FSH($command))->getResponseData();
        } else {
            $command = new SSC();
            $command->setUuid(strval($uuid));
            $responseValueObject = (new SSH($command))->getResponseData();
        }

        return new Response(
            [
                'statusCode' => $responseValueObject->getResponseCode(),
                'format' => Response::FORMAT_JSON,
                'data' => [
                    'email' => $responseValueObject->getEmail()
                ]
            ]
        );
    }
}