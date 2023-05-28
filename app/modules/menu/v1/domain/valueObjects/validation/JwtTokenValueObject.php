<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\validation;

use app\components\JWTComponent;
use app\exceptions\ValidationException;

final class JwtTokenValueObject
{
    private string $token;

    public function __construct(string $token, ?string $attributeName = null)
    {
        /** @var JWTComponent $jwt */
        $jwt = yiiWebApp()->get('jwt');

        if (!$jwt->isValid($token)) {
            throw new ValidationException(
                $attributeName ?? 'message',
                'Token is expired'
            );
        }

        $this->token = $token;
    }

    public function getValue(): string
    {
        return $this->token;
    }
}