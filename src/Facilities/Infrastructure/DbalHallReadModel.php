<?php

namespace App\Facilities\Infrastructure;

use App\Facilities\Application\Model\HallDto;
use App\Facilities\Application\Model\HallListDto;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Application\Ports\HallReadModel;
use App\Facilities\Domain\Values\HallCapacity;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\SerializerInterface;

class DbalHallReadModel implements HallReadModel
{
    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer,
    ) {
    }

    public function readHall(HallId $id): ?HallDto
    {
        $row = $this->fetchHallRow($id);
        if (null !== $row) {
            return $this->createHallDtoFromRow($row);
        } else {
            return null;
        }
    }

    public function readHallLayout(HallId $id): ?SeatingLayoutDto
    {
        $layout = $this->fetchHallLayout($id);
        if (null !== $layout) {
            return $this->deserializeSeatingLayout($layout);
        } else {
            return null;
        }
    }

    public function readHalls(): HallListDto
    {
        $total = $this->countHalls();
        $items = array_map(
            fn (array $row) => $this->createHallDtoFromRow($row),
            $this->fetchHallsRows(),
        );

        return new HallListDto($total, $items);
    }

    private function fetchHallRow(HallId $id): ?array
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT id, status, name, capacity
FROM facilities_hall
WHERE id = ?
SQL,
            [$id],
        );

        return $statement->fetchAssociative() ?: null;
    }

    private function fetchHallLayout(HallId $id): ?string
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT layout
FROM facilities_hall
WHERE id = ?
SQL,
            [$id],
        );

        return $statement->fetchOne() ?: null;
    }

    private function countHalls(): int
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT COUNT(*)
FROM facilities_hall
SQL,
        );

        return (int) $statement->fetchOne();
    }

    private function fetchHallsRows(): array
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT id, status, name, capacity
FROM facilities_hall
ORDER BY id
SQL);

        return $statement->fetchAllAssociative();
    }

    private function createHallDtoFromRow(array $row): HallDto
    {
        return new HallDto(
            id: new HallId($row['id']),
            status: HallStatus::from($row['status']),
            name: new HallName($row['name']),
            capacity: new HallCapacity((int) $row['capacity']),
        );
    }

    private function deserializeSeatingLayout(string $json): SeatingLayoutDto
    {
        return $this->serializer->deserialize($json, SeatingLayoutDto::class, 'json');
    }
}
