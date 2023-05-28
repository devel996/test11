<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\swaggerSchemas\objects;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "EmailObject",
    type: "string",
    example: "test@mail.com"
)]
interface EmailObject
{

}