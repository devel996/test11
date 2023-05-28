<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\swaggerSchemas\objects;

use OpenApi\Attributes as OA;

#[OA\SecurityScheme(
    securityScheme: "BearerAuthObject",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
interface BearerAuthObject
{

}