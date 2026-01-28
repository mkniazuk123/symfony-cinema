<?php

declare(strict_types=1);

namespace App\Tests\Core\Context;

use Behat\Behat\Context\Context;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeScenario;
use Doctrine\DBAL\Connection;

final class DatabaseContext implements Context
{
    public function __construct(
        private Connection $database,
    ) {
    }

    #[BeforeScenario]
    public function startTransaction(): void
    {
        $this->database->executeStatement(<<<SQL
TRUNCATE TABLE
    catalog_movie,
    doctrine_migration_versions,
    facilities_hall,
    facilities_reservation,
    planning_hall,
    planning_movie,
    planning_screening
RESTART IDENTITY CASCADE
SQL);
        $this->database->beginTransaction();
    }

    #[AfterScenario]
    public function rollbackTransaction(): void
    {
        $this->database->rollBack();
    }
}
