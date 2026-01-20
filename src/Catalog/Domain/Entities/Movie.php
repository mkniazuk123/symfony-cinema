<?php

namespace App\Catalog\Domain\Entities;

use App\Catalog\Domain\Events\MovieArchived;
use App\Catalog\Domain\Events\MovieCreated;
use App\Catalog\Domain\Events\MovieDetailsUpdated;
use App\Catalog\Domain\Events\MovieLengthUpdated;
use App\Catalog\Domain\Events\MovieReleased;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;
use App\Core\Domain\AggregateRoot;

/**
 * @extends AggregateRoot<MovieId>
 */
class Movie extends AggregateRoot
{
    public static function create(MovieId $id, MovieDetails $details, MovieLength $length): self
    {
        $status = MovieStatus::UPCOMING;

        return new self($id, $status, $details, $length, created: true);
    }

    public static function reconstitute(MovieId $id, MovieStatus $status, MovieDetails $details, MovieLength $length): self
    {
        return new self($id, $status, $details, $length);
    }

    private function __construct(
        MovieId $id,
        private MovieStatus $status,
        private MovieDetails $details,
        private MovieLength $length,
        bool $created = false,
    ) {
        parent::__construct($id);

        if ($created) {
            $this->recordEvent(new MovieCreated($id, $status, $details, $length));
        }
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
        $this->recordEvent(new MovieReleased($this->id));
    }

    /**
     * @throws InvalidMovieStatusException
     */
    public function archive(): void
    {
        $this->assertStatus(MovieStatus::UPCOMING, MovieStatus::AVAILABLE);
        $this->status = MovieStatus::ARCHIVED;
        $this->recordEvent(new MovieArchived($this->id));
    }

    /**
     * @throws InvalidMovieStatusException
     */
    public function updateDetails(MovieDetails $details): void
    {
        $this->assertStatus(MovieStatus::UPCOMING, MovieStatus::AVAILABLE);

        if (!$this->details->equals($details)) {
            $this->details = $details;
            $this->recordEvent(new MovieDetailsUpdated($this->id, $details));
        }
    }

    /**
     * @throws InvalidMovieStatusException
     */
    public function updateLength(MovieLength $length): void
    {
        $this->assertStatus(MovieStatus::UPCOMING, MovieStatus::AVAILABLE);
        if (!$this->length->equals($length)) {
            $this->length = $length;
            $this->recordEvent(new MovieLengthUpdated($this->id, $length));
        }
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
