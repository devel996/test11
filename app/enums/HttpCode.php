<?php

declare(strict_types=1);

namespace app\enums;

enum HttpCode: int
{
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case SERVER_ERROR = 500;
}