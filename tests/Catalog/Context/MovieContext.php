<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Context;

use App\Catalog\Application\Command\ArchiveMovie;
use App\Catalog\Application\Command\CreateMovie;
use App\Catalog\Application\Command\ReleaseMovie;
use App\Catalog\Application\Command\UpdateMovieDetails;
use App\Catalog\Application\Command\UpdateMovieLength;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Application\Model\MovieListDto;
use App\Catalog\Application\Query\GetMovie;
use App\Catalog\Application\Query\ListMovies;
use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;
use App\Catalog\Domain\Values\MovieTitle;
use App\Core\Application\CommandBus;
use App\Core\Application\QueryBus;
use App\Tests\Catalog\Fixtures\MovieBuilder;
use App\Tests\Catalog\Fixtures\MovieDetailsBuilder;
use Behat\Behat\Context\Context;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Behat\Transformation\Transform;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Webmozart\Assert\Assert;
use Zenstruck\Messenger\Test\Bus\TestBus;
use Zenstruck\Messenger\Test\Bus\TestBusRegistry;
use Zenstruck\Messenger\Test\Transport\TestTransport;

final class MovieContext implements Context
{
    private mixed $response = null;
    private ?\Throwable $error = null;

    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private MovieRepository $movieRepository,
        #[Autowire('@zenstruck_messenger_test.bus_registry')]
        private TestBusRegistry $testBusRegistry,
    ) {
    }

    #[BeforeScenario]
    public function reset(): void
    {
        TestTransport::resetAll();
        TestBus::resetAll();

        $this->response = null;
        $this->error = null;
    }

    #[Given('There is a movie :id')]
    #[Given('There is a movie :id with title :title')]
    #[Given('There is a movie :id with length :length minutes')]
    #[Given('There is a movie :id with status :status')]
    #[Given('There is a movie :id with status :status and title :title')]
    #[Given('There is a movie :id with status :status and length :length minutes')]
    public function thereIsAMovie(
        MovieId $id,
        ?MovieTitle $title = null,
        ?MovieLength $length = null,
        ?MovieStatus $status = null,
    ): void {
        $details = MovieDetailsBuilder::create();
        if (null !== $title) {
            $details->withTitle($title);
        }

        $movie = MovieBuilder::reconstitute($id)->withDetails($details->build());
        if (null !== $length) {
            $movie->withLength($length);
        }
        if (null !== $status) {
            $movie->withStatus($status);
        }

        $this->movieRepository->save($movie->build());
    }

    #[When('I create a movie :title with length :length minutes')]
    public function iCreateAMovieWithLengthMinutes(MovieTitle $title, MovieLength $length): void
    {
        $id = MovieId::generate();
        $details = MovieDetailsBuilder::create()
            ->withTitle($title)
            ->build();

        $this->execute(function () use ($id, $details, $length) {
            $this->commandBus->dispatch(new CreateMovie($id, $details, $length));

            return $id;
        });
    }

    #[When('I retrieve a movie list')]
    public function iRetrieveAMovieList(): void
    {
        $this->execute(fn () => $this->queryBus->query(new ListMovies()));
    }

    #[When('I retrieve the movie :id')]
    public function iRetrieveTheMovie(MovieId $id): void
    {
        $this->execute(fn () => $this->queryBus->query(new GetMovie($id)));
    }

    #[When('I update the movie :id details with title :title')]
    public function iUpdateTheMovieDetailsWithTitle(MovieId $id, MovieTitle $title): void
    {
        $details = MovieDetailsBuilder::create()->withTitle($title)->build();
        $this->execute(fn () => $this->commandBus->dispatch(new UpdateMovieDetails($id, $details)));
    }

    #[When('I update the movie :id length to :length minutes')]
    public function iUpdateTheMovieLengthToMinutes(MovieId $id, MovieLength $length): void
    {
        $this->execute(fn () => $this->commandBus->dispatch(new UpdateMovieLength($id, $length)));
    }

    #[When('I release the movie :id')]
    public function iReleaseTheMovie(MovieId $id): void
    {
        $this->execute(fn () => $this->commandBus->dispatch(new ReleaseMovie($id)));
    }

    #[When('I archive the movie :id')]
    public function iArchiveTheMovie(MovieId $id): void
    {
        $this->execute(fn () => $this->commandBus->dispatch(new ArchiveMovie($id)));
    }

    #[Then('The movie should be created successfully')]
    public function theMovieShouldBeCreatedSuccessfully(): void
    {
        $movieId = $this->getResult(MovieId::class);
        $this->response = $this->movieRepository->find($movieId);
    }

    #[Then('The movie :id should exists')]
    public function theMovieShouldExists(MovieId $id): void
    {
        $this->response = $this->movieRepository->find($id);
        Assert::notNull($this->response, 'Expected movie to exist.');
    }

    #[Then('The movie should have title :title')]
    public function theMovieShouldHaveTitle(MovieTitle $title): void
    {
        $movie = $this->getResult(Movie::class);
        Assert::eq($title, $movie->getDetails()->title);
    }

    #[Then('The movie should have length :length minutes')]
    public function theMovieShouldHaveLength(MovieLength $length): void
    {
        $movie = $this->getResult(Movie::class);
        Assert::eq($length, $movie->getLength());
    }

    #[Then('The movie should have status :status')]
    public function theMovieShouldHaveStatus(MovieStatus $status): void
    {
        $movie = $this->getResult(Movie::class);
        Assert::eq($status, $movie->getStatus());
    }

    #[Then('The movie should be retrieved successfully')]
    public function theMovieShouldBeRetrievedSuccessfully(): void
    {
        $this->getResult(MovieDto::class);
    }

    #[Then('There should be a movie not found error')]
    public function thereShouldBeAMovieNotFoundError(): void
    {
        $this->assertException(MovieNotFoundException::class);
    }

    #[Then('There should be an invalid movie status error')]
    public function thereShouldBeAnInvalidMovieStatusError(): void
    {
        $this->assertException(InvalidMovieStatusException::class);
    }

    #[Then('The movie list total should be :count')]
    public function theMovieListTotalShouldBe(int $count): void
    {
        $list = $this->getResult(MovieListDto::class);
        Assert::eq($count, $list->total);
    }

    #[Then('The movie list should contain :count items')]
    public function theMovieListShouldContainItems(int $count): void
    {
        $list = $this->getResult(MovieListDto::class);
        Assert::count($list->items, $count);
    }

    #[Then('Integration event :class should be published')]
    public function integrationEventShouldBePublished(string $class): void
    {
        $class = 'App\\Catalog\\API\\Events\\'.$class;
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Event class %s does not exist.', $class));
        }
        $messages = $this->testBusRegistry->get('integration.bus')->dispatched()->messages($class);
        Assert::notEmpty($messages, sprintf('Expected integration event of type %s to be published.', $class));
    }

    private function execute(callable $action): void
    {
        $this->response = null;
        $this->error = null;

        try {
            $this->response = $action();
        } catch (\Throwable $e) {
            $this->error = $e;
        }
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    private function getResult(string $class): object
    {
        Assert::isInstanceOf($this->response, $class, sprintf('Expected result of type %s.', $class));
        assert($this->response instanceof $class); // for PHPStn

        return $this->response;
    }

    /**
     * @param class-string<\Throwable> $class
     */
    private function assertException(string $class): void
    {
        Assert::isInstanceOf($this->error, $class, sprintf('Expected exception of type %s.', $class));
    }

    #[Transform(':id')]
    public function transformId(string $id): MovieId
    {
        return new MovieId($id);
    }

    #[Transform(':title')]
    public function transformTitle(string $title): MovieTitle
    {
        return new MovieTitle($title);
    }

    #[Transform(':length')]
    public function transformMinutes(int $length): MovieLength
    {
        return new MovieLength($length);
    }

    #[Transform(':status')]
    public function transformStatus(string $status): MovieStatus
    {
        return MovieStatus::from($status);
    }
}
