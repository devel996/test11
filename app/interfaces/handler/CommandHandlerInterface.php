<?php

namespace app\interfaces\handler;

use app\interfaces\response\ResponseValueObjectInterface;

interface CommandHandlerInterface
{
    public function getResponseData(): ResponseValueObjectInterface;
}