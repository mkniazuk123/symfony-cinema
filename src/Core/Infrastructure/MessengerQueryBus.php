<?php

namespace App\Core\Infrastructure;

use App\Core\Application\Query;
use App\Core\Application\QueryBus;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerQueryBus implements QueryBus
{
    use HandleTrait;

    public function __construct(
        /* @phpstan-ignore property.onlyWritten */
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $messageBus,
    ) {
    }

    public function query(Query $query): mixed
    {
        try {
            return $this->handle($query);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }
    }
}
