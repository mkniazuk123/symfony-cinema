<?php

declare(strict_types=1);

namespace App\Tests\Facilities\Context;

use App\Core\Application\CommandBus;
use App\Facilities\Application\Command\ArchiveHallCommand;
use App\Facilities\Application\Command\CloseHallCommand;
use App\Facilities\Application\Command\CreateHallCommand;
use App\Facilities\Application\Command\OpenHallCommand;
use App\Facilities\Application\Command\RenameHallCommand;
use App\Facilities\Application\Command\UpdateHallLayoutCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\InvalidLayoutException;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Values\HallCapacity;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;
use App\Tests\Facilities\Fixtures\HallBuilder;
use App\Tests\Facilities\Fixtures\SeatingLayoutBuilder;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Behat\Transformation\Transform;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

final class HallContext implements Context
{
    private mixed $response = null;
    private ?\Throwable $error = null;

    public function __construct(
        private CommandBus $commandBus,
        private HallRepository $hallRepository,
        private SerializerInterface $serializer,
    ) {
    }

    #[BeforeScenario]
    public function reset(): void
    {
        $this->response = null;
        $this->error = null;
    }

    #[Given('There is a hall :id')]
    #[Given('There is a hall :id named :name')]
    #[Given('There is a :status hall :id')]
    #[Given('There is an :status hall :id')]
    #[Given('There is a :status hall :id named :name')]
    #[Given('There is an :status hall :id named :name')]
    #[Given('There is a :status hall :id with capacity of :capacity seats')]
    #[Given('There is an :status hall :id with capacity of :capacity seats')]
    #[Given('There is a :status hall :id with capacity of :capacity seats and the following layout:')]
    #[Given('There is an :status hall :id with capacity of :capacity seats and the following layout:')]
    public function thereIsAHall(
        HallId $id,
        ?HallName $name = null,
        ?HallStatus $status = null,
        ?PyStringNode $layout = null,
        ?HallCapacity $capacity = null,
    ): void {
        $hall = HallBuilder::reconstitute()->withId($id);
        if (null !== $name) {
            $hall->withName($name);
        }
        if (null !== $status) {
            $hall->withStatus($status);
        }
        if (null !== $layout) {
            $layout = $this->serializer->deserialize(
                (string) $layout,
                SeatingLayoutDto::class,
                'json',
            );
            $hall->withLayout($layout->toDomain());
        }
        if (null !== $capacity) {
            $hall->withCapacity($capacity);
        }

        $this->hallRepository->save($hall->build());
    }

    #[When('I create a hall named :name with the following layout:')]
    #[When('I create a hall with the following layout:')]
    public function iCreateAHall(
        ?HallName $name = null,
        ?PyStringNode $layout = null,
    ): void {
        $name ??= new HallName('Default Hall Name');
        if (null !== $layout) {
            $layout = $this->transformLayout($layout);
        } else {
            $layout = SeatingLayoutDto::fromDomain(new SeatingLayoutBuilder()->build());
        }

        $this->execute(function () use ($name, $layout) {
            $id = HallId::generate();
            $command = new CreateHallCommand($id, $name, $layout);
            $this->commandBus->dispatch($command);

            return $id;
        });
    }

    #[When('I rename the hall :id to :name')]
    public function iRenameTheHall(
        HallId $id,
        HallName $name,
    ): void {
        $command = new RenameHallCommand($id, $name);
        $this->execute(fn () => $this->commandBus->dispatch($command));
    }

    #[When('I update the hall :id layout to:')]
    public function iUpdateTheHallLayoutTo(HallId $id, PyStringNode $layout): void
    {
        $layout = $this->transformLayout($layout);
        $command = new UpdateHallLayoutCommand($id, $layout);
        $this->execute(fn () => $this->commandBus->dispatch($command));
    }

    #[When('I update the hall :id layout')]
    #[When('I update the hall :id layout to :seats seats')]
    public function iUpdateTheHallLayout(
        HallId $id,
        ?int $seats = null,
    ): void {
        $layout = new SeatingLayoutBuilder()
            ->addSampleRow($seats ?? 1);
        $layout = SeatingLayoutDto::fromDomain($layout->build());
        $command = new UpdateHallLayoutCommand($id, $layout);
        $this->execute(fn () => $this->commandBus->dispatch($command));
    }

    #[When('I open the hall :id')]
    public function iOpenTheHall(HallId $id): void
    {
        $command = new OpenHallCommand($id);
        $this->execute(fn () => $this->commandBus->dispatch($command));
    }

    #[When('I close the hall :id')]
    public function iCloseTheHall(HallId $id): void
    {
        $command = new CloseHallCommand($id);
        $this->execute(fn () => $this->commandBus->dispatch($command));
    }

    #[When('I archive the hall :id')]
    public function iArchiveTheHall(HallId $id): void
    {
        $command = new ArchiveHallCommand($id);
        $this->execute(fn () => $this->commandBus->dispatch($command));
    }

    #[Then('The hall should be created successfully')]
    public function theHallShouldBeCreatedSuccessfully(): void
    {
        $hallId = $this->getResult(HallId::class);
        $this->response = $this->hallRepository->find($hallId);
        Assert::notNull($this->response, 'Hall was not found in the repository after creation.');
    }

    #[Then('The hall :id should exists')]
    public function theHallShouldExists(HallId $id): void
    {
        $this->response = $this->hallRepository->find($id);
        Assert::notNull($this->response, 'Expected hall to exist.');
    }

    #[Then('The hall should be named :name')]
    #[Then('The hall should have capacity of :capacity seats')]
    #[Then('The hall should be :status')]
    public function theHallShouldBe(
        ?HallName $name = null,
        ?HallCapacity $capacity = null,
        ?HallStatus $status = null,
    ): void {
        $hall = $this->getResult(Hall::class);
        if (null !== $name) {
            Assert::eq($hall->getName(), $name);
        }
        if (null !== $capacity) {
            Assert::eq($hall->getCapacity(), $capacity);
        }
        if (null !== $status) {
            Assert::eq($hall->getStatus(), $status);
        }
    }

    #[Then('There should be an invalid layout error with message :message')]
    public function thereShouldBeAnInvalidLayoutError(string $message): void
    {
        $exception = $this->assertException(InvalidLayoutException::class);
        Assert::eq($exception->getMessage(), $message);
    }

    #[Then('There should be a hall not found error')]
    public function thereShouldBeAHallNotFoundError(): void
    {
        $this->assertException(HallNotFoundException::class);
    }

    #[Then('There should be an invalid hall status error')]
    public function thereShouldBeAnInvalidHallStatusError(): void
    {
        $this->assertException(InvalidHallStatusException::class);
    }

    #[Transform(':id')]
    public function transformId(string $id): HallId
    {
        return new HallId($id);
    }

    #[Transform(':name')]
    public function transformName(string $name): HallName
    {
        return new HallName($name);
    }

    #[Transform(':capacity')]
    public function transformCapacity(string $capacity): HallCapacity
    {
        return new HallCapacity((int) $capacity);
    }

    #[Transform(':status')]
    public function transformStatus(string $status): HallStatus
    {
        return HallStatus::from($status);
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
    private function getResult(string $class): mixed
    {
        Assert::isInstanceOf($this->response, $class);
        assert($this->response instanceof $class); // for PHPStan

        return $this->response;
    }

    /**
     * @template T of \Throwable
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    private function assertException(string $class): \Throwable
    {
        Assert::isInstanceOf($this->error, $class);
        assert($this->error instanceof $class); // for PHPStan

        return $this->error;
    }

    private function transformLayout(PyStringNode $layout): SeatingLayoutDto
    {
        return $this->serializer->deserialize(
            (string) $layout,
            SeatingLayoutDto::class,
            'json',
        );
    }
}
