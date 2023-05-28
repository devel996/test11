<?php

declare(strict_types=1);

namespace app\exceptions;

use app\enums\HttpCode;
use Exception;
use Throwable;

class ValidationException extends Exception
{
    public function __construct(string $attributeName, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->code = HttpCode::BAD_REQUEST->value;

        parent::__construct(
            $this->getDecoratedMessage($attributeName, $message),
            $code,
            $previous
        );
    }

    private function getDecoratedMessage(string $attributeName, string $message): string
    {
        $data = json_encode(
            [
                $attributeName => [$message]
            ]
        );

        if ($data === false) {
            return '{"message":"Something went wrong"}';
        }

        return $data;
    }
}