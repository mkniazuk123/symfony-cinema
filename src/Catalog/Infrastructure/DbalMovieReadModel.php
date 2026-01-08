<?php

namespace App\Catalog\Infrastructure;

use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Application\Model\MovieListDto;
use App\Catalog\Application\Ports\MovieReadModel;
use App\Catalog\Domain\Values\MovieDescription;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;
use App\Catalog\Domain\Values\MovieTitle;
use Doctrine\DBAL\Connection;

class DbalMovieReadModel implements MovieReadModel
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function readMovie(MovieId $id): ?MovieDto
    {
        $row = $this->fetchMovieRow($id);
        if (null !== $row) {
            return $this->createMovieDtoFromRow($row);
        } else {
            return null;
        }
    }

    public function readMovies(): MovieListDto
    {
        $total = $this->countMovies();
        $items = array_map(
            fn (array $row) => $this->createMovieDtoFromRow($row),
            $this->fetchMoviesRows(),
        );

        return new MovieListDto($total, $items);
    }

    private function fetchMovieRow(MovieId $id): ?array
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT id, status, title, description, length
FROM catalog_movie
WHERE id = ?
SQL,
            [$id],
        );

        return $statement->fetchAssociative() ?: null;
    }

    private function countMovies(): int
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT COUNT(*)
FROM catalog_movie
SQL,
        );

        return (int) $statement->fetchOne();
    }

    private function fetchMoviesRows(): array
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT id, status, title, description, length
FROM catalog_movie
ORDER BY id
SQL);

        return $statement->fetchAllAssociative();
    }

    private function createMovieDtoFromRow(array $row): MovieDto
    {
        return new MovieDto(
            id: new MovieId($row['id']),
            status: MovieStatus::from($row['status']),
            details: new MovieDetails(
                title: new MovieTitle($row['title']),
                description: new MovieDescription($row['description']),
            ),
            length: new MovieLength((int) $row['length']),
        );
    }
}
