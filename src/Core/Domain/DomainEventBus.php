<?php

namespace App\Core\Domain;

interface DomainEventBus
{
    public function dispatch(DomainEvent $event): void;
}
