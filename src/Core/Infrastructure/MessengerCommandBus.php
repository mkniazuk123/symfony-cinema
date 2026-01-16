<?php

namespace App\Core\Infrastructure;

use App\Core\Application\Command;
use App\Core\Application\CommandBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBus implements CommandBus
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function dispatch(Command $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }
    }
}
