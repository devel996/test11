<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\validation;

use app\exceptions\ValidationException;

final class IpValueObject
{
    private string $ip;

    public function __construct(string $ip, string $attributeName = 'ip')
    {
        if (empty($ip)) {
            throw new ValidationException(
                $attributeName,
                'Ip is not correct'
            );
        }

        $this->ip = $ip;
    }

    public function getValue(): string
    {
        return $this->ip;
    }
}