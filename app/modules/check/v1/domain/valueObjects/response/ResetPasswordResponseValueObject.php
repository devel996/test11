<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\valueObjects\response;

use app\interfaces\response\ResponseValueObjectInterface;
use app\traits\ResponseCodeTrait;

final class ResetPasswordResponseValueObject implements ResponseValueObjectInterface
{
    use ResponseCodeTrait;

    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}