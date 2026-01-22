<?php

namespace App\Planning\Infrastructure;

use App\Planning\Domain\Entities\Hall;
use App\Planning\Domain\Ports\HallRepository;
use App\Planning\Domain\Values\HallId;
use Doctrine\DBAL\Connection;

class DbalHallRepository implements HallRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function find(HallId $id): ?Hall
    {
        if (!$this->connection->isTransactionActive()) {
            throw new \RuntimeException(sprintf('%s::%s() must be called inside a transaction.', __CLASS__, __METHOD__));
        }

        $row = $this->fetchRow($id);
        if (null !== $row) {
            return $this->reconstituteHallFromRow($row);
        } else {
            return null;
        }
    }

    public function save(Hall $hall): void
    {
        $this->connection->executeStatement(<<<SQL
INSERT INTO planning_hall (id, open)
VALUES (?, ?)
ON CONFLICT (id)
DO UPDATE
SET open = EXCLUDED.open
SQL,
            [
                $hall->getId(),
                (int) $hall->isOpen(),
            ],
        );
    }

    private function fetchRow(HallId $id): ?array
    {
        $result = $this->connection->executeQuery(<<<SQL
SELECT id, open
FROM planning_hall
WHERE id = ?
FOR UPDATE
SQL,
            [$id],
        );

        return $result->fetchAssociative() ?: null;
    }

    private function reconstituteHallFromRow(array $row): Hall
    {
        return Hall::reconstitute(
            id: new HallId($row['id']),
            open: (bool) $row['open'],
        );
    }
}
