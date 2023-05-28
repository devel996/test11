<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\swaggerSchemas\objects;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "JwtTokenObject",
    required: ["token", "expiresAt"],
    properties: [
        new OA\Property(
            property: "token",
            type: "string",
            example: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.iJ3ZWJsYXRlM19pc3N1ZWRfYnkiLCJleHAiOjE2ODY0O.DgwOTQuNTcyNTM1LCJ1aWQiOjEsInJhbmQiOiIxNjgzODA5Njk0MTY3In0"
        ),
        new OA\Property(
            property: "expiresAt",
            properties: [
                new OA\Property(
                    property: "date",
                    type: "string",
                    example: "2023-06-11 12:54:54.572535"
                ),
                new OA\Property(
                    property: "timezone_type",
                    type: "integer",
                    example: 1
                ),
                new OA\Property(
                    property: "timezone",
                    type: "string",
                    example: "+00:00"
                )
            ],
            type: "object"
        ),
    ],
    type: "object"
)]
interface JwtTokenObject
{

}