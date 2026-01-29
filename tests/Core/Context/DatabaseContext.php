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
        // TODO: Deduplicate
        $this->database->executeStatement(<<<SQL
TRUNCATE TABLE
    catalog_movie,
    facilities_hall,
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
