<?php

namespace App\Catalog\Domain\Entities;

use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;

class Movie
{
    public static function create(MovieId $id, MovieDetails $details, MovieLength $length): self
    {
        $status = MovieStatus::UPCOMING;

        return new self($id, $status, $details, $length);
    }

    public static function reconstitute(MovieId $id, MovieStatus $status, MovieDetails $details, MovieLength $length): self
    {
        return new self($id, $status, $details, $length);
    }

    private function __construct(
        private MovieId $id,
        private MovieStatus $status,
        private MovieDetails $details,
        private MovieLength $length,
    ) {
    }

    public function getId(): MovieId
    {
        return $this->id;
    }

    public function getStatus(): MovieStatus
    {
        return $this->status;
    }

    public function getDetails(): MovieDetails
    {
        return $this->details;
    }

    public function getLength(): MovieLength
    {
        return $this->length;
    }

    /**
     * @throws InvalidMovieStatusException
     */
    public function release(): void
    {
        $this->assertStatus(MovieStatus::UPCOMING);

        $this->status = MovieStatus::AVAILABLE;
    }

    /**
     * @throws InvalidMovieStatusException
     */
    public function archive(): void
    {
        $this->assertStatus(MovieStatus::UPCOMING, MovieStatus::AVAILABLE);

        $this->status = MovieStatus::ARCHIVED;
    }

    /**
     * @throws InvalidMovieStatusException
     */
    public function updateDetails(MovieDetails $details): void
    {
        $this->assertStatus(MovieStatus::UPCOMING, MovieStatus::AVAILABLE);

        $this->details = $details;
    }

    /**
     * @throws InvalidMovieStatusException
     */
    public function updateLength(MovieLength $length): void
    {
        $this->assertStatus(MovieStatus::UPCOMING, MovieStatus::AVAILABLE);

        $this->length = $length;
    }

    /**
     * @throws InvalidMovieStatusException
     */
    private function assertStatus(MovieStatus ...$statuses): void
    {
        if (!in_array($this->status, $statuses, true)) {
            throw new InvalidMovieStatusException($this->id, $this->status);
        }
    }
}
