<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\validation;

use app\exceptions\ValidationException;

final class PasswordValueObject
{
    private string $password;

    public function __construct(string $password, string $attributeName = 'password')
    {
        if (empty($password)) {
            throw new ValidationException(
                $attributeName,
                'Value can not be empty'
            );
        }

        $this->password = $password;
    }

    public function getValue(): string
    {
        return $this->password;
    }
}