<?php

namespace App\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

abstract class IntegrationTestCase extends KernelTestCase
{
    use InteractsWithMessenger;

    protected Application $console;
    protected MessageBusInterface $messageBus;
    protected MessageBusInterface $jobBus;
    protected MessageBusInterface $domainEventBus;
    protected Connection $database;
    protected EntityManagerInterface $entityManager;
    protected EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();
        $this->console = new Application(self::$kernel ?? throw new \RuntimeException('kernel not found'));
        $this->messageBus = $container->get('message.bus');
        $this->jobBus = $container->get('job.bus');
        $this->domainEventBus = $container->get('domain_event.bus');
        $this->database = $container->get(Connection::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->eventDispatcher = $container->get('event_dispatcher');

        $this->resetDatabase();
    }

    private function resetDatabase(): void
    {
        // TODO: Get rid of hardcoded table names
        $this->database->executeStatement(<<<SQL
TRUNCATE TABLE
    catalog_movie
RESTART IDENTITY CASCADE
SQL);
    }
}
