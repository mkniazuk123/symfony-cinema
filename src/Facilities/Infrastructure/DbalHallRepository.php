<?php

namespace App\Facilities\Infrastructure;

use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Values\HallCapacity;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;
use App\Facilities\Domain\Values\SeatingLayout;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\SerializerInterface;

class DbalHallRepository implements HallRepository
{
    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer,
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
INSERT INTO facilities_hall (id, status, name, capacity, layout)
VALUES (?, ?, ?, ?, ?)
ON CONFLICT (id)
DO UPDATE
SET status = EXCLUDED.status,
    name = EXCLUDED.name,
    capacity = EXCLUDED.capacity,
    layout = EXCLUDED.layout
SQL,
            [
                $hall->getId(),
                $hall->getStatus()->value,
                $hall->getName(),
                $hall->getCapacity()->value(),
                $this->serializeSeatingLayout($hall->getLayout()),
            ],
        );
    }

    private function fetchRow(HallId $id): ?array
    {
        $result = $this->connection->executeQuery(<<<SQL
SELECT id, status, name, capacity, layout
FROM facilities_hall
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
            status: HallStatus::from($row['status']),
            name: new HallName($row['name']),
            capacity: new HallCapacity((int) $row['capacity']),
            layout: $this->deserializeSeatingLayout($row['layout']),
        );
    }

    private function serializeSeatingLayout(SeatingLayout $layout): string
    {
        $layout = SeatingLayoutDto::fromDomain($layout);

        return $this->serializer->serialize($layout, 'json');
    }

    private function deserializeSeatingLayout(string $json): SeatingLayout
    {
        /** @var SeatingLayoutDto $layoutDto */
        $layoutDto = $this->serializer->deserialize($json, SeatingLayoutDto::class, 'json');

        return $layoutDto->toDomain();
    }
}
