<?php

namespace App\Core\Application;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
