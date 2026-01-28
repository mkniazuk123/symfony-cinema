<?php

namespace App\Core\Application;

interface IntegrationBus
{
    public function dispatch(IntegrationEvent $event): void;
}
