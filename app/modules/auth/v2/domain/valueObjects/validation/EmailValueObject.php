<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\validation;

use app\exceptions\ValidationException;

final class EmailValueObject
{
    private string $email;

    public function __construct(string $email, string $attributeName = 'email')
    {
        if (empty($email)) {
            throw new ValidationException(
                $attributeName,
                'Value can not be empty'
            );
        }

        if(!boolval(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            throw new ValidationException(
                $attributeName,
                'Incorrect value'
            );
        }

        $this->email = $email;
    }

    public function getValue(): string
    {
        return $this->email;
    }
}