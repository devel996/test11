<?php

namespace app\interfaces\response;

interface ResponseValueObjectInterface
{
    public function getResponseCode(): int;
    public function setResponseCode(int $responseCode): void;
}