<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\swaggerSchemas\responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UnauthorizedObject",
    required: ["name", "message", "code", "status", "type"],
    properties: [
        new OA\Property(
            property: "name",
            type: "string",
            example: "Unauthorized"
        ),
        new OA\Property(
            property: "message",
            type: "string",
            example: "Your request was made with invalid credentials."
        ),
        new OA\Property(
            property: "code",
            type: "integer",
            example: 0
        ),
        new OA\Property(
            property: "status",
            type: "integer",
            example: 401
        ),
        new OA\Property(
            property: "type",
            type: "string",
            example: "yii\web\UnauthorizedHttpException"
        )
    ],
    type: "object"
)]
interface UnauthorizedObject
{

}