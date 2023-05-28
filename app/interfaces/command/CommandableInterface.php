<?php

namespace app\interfaces\command;

interface CommandableInterface
{
    public function getCommand(): CommandInterface;
}