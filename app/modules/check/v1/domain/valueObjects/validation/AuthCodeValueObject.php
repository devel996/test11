<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\validation;

use app\exceptions\ValidationException;

final class AuthCodeValueObject
{
    private int $authCode;

    public function __construct(int $authCode, string $attributeName = 'auth_code')
    {
        if (empty($authCode)) {
            throw new ValidationException(
                $attributeName,
                'Value can not be empty'
            );
        }

        if (!$this->isCorrectAuthCode($authCode)) {
            throw new ValidationException(
                $attributeName,
                'Incorrect value'
            );
        }

        $this->authCode = $authCode;
    }

    public function getValue(): int
    {
        return $this->authCode;
    }

    private function isCorrectAuthCode(int $authCode): bool
    {
        return $authCode >= 100000 && $authCode <= 999999;
    }
}