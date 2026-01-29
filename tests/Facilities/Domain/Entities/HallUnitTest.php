<?php

namespace App\Tests\Facilities\Domain\Entities;

use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;
use App\Tests\Facilities\Fixtures\HallBuilder;
use App\Tests\Facilities\Fixtures\SeatingLayoutBuilder;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class HallUnitTest extends TestCase
{
    public function testNewHallIsOpen(): void
    {
        // Arrange:

        // Act:
        $hall = HallBuilder::create()->build();

        // Assert:
        $this->assertEquals(HallStatus::OPEN, $hall->getStatus());
    }

    public function testCapacityIsCalculatedOnCreation(): void
    {
        // Arrange:
        $layout = new SeatingLayoutBuilder()
            ->addSampleRow(10)
            ->build();

        // Act:
        $hall = HallBuilder::create()
            ->withLayout($layout)
            ->build();

        // Assert:
        $this->assertEquals(10, $hall->getCapacity()->value());
    }

    #[TestWith([HallStatus::OPEN])]
    #[TestWith([HallStatus::CLOSED])]
    public function testRenameHall(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        $newName = new HallName('New Hall Name');

        // Act:
        $hall->rename($newName);

        // Assert:
        $this->assertEquals($newName, $hall->getName());
    }

    #[TestWith([HallStatus::ARCHIVED])]
    public function testCannotRenameHall(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        // Assert:
        $this->expectException(InvalidHallStatusException::class);

        // Act:
        $hall->rename(new HallName('New Hall Name'));
    }

    #[TestWith([HallStatus::OPEN])]
    #[TestWith([HallStatus::CLOSED])]
    public function testUpdateLayout(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        $newLayout = new SeatingLayoutBuilder()
            ->addSampleRow(20)
            ->build();

        // Act:
        $hall->updateLayout($newLayout);

        // Assert:
        $this->assertEquals($newLayout, $hall->getLayout());
        $this->assertEquals(20, $hall->getCapacity()->value());
    }

    #[TestWith([HallStatus::ARCHIVED])]
    public function testCannotUpdateLayout(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        $newLayout = new SeatingLayoutBuilder()
            ->addSampleRow()
            ->build();

        // Assert:
        $this->expectException(InvalidHallStatusException::class);

        // Act:
        $hall->updateLayout($newLayout);
    }

    #[TestWith([HallStatus::OPEN])]
    public function testClose(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        // Act:
        $hall->close();

        // Assert:
        $this->assertEquals(HallStatus::CLOSED, $hall->getStatus());
    }

    #[TestWith([HallStatus::CLOSED])]
    #[TestWith([HallStatus::ARCHIVED])]
    public function testCannotClose(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        // Assert:
        $this->expectException(InvalidHallStatusException::class);

        // Act:
        $hall->close();
    }

    #[TestWith([HallStatus::CLOSED])]
    public function testOpen(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        // Act:
        $hall->open();

        // Assert:
        $this->assertEquals(HallStatus::OPEN, $hall->getStatus());
    }

    #[TestWith([HallStatus::OPEN])]
    #[TestWith([HallStatus::ARCHIVED])]
    public function testCannotOpen(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        // Assert:
        $this->expectException(InvalidHallStatusException::class);

        // Act:
        $hall->open();
    }

    #[TestWith([HallStatus::OPEN])]
    #[TestWith([HallStatus::CLOSED])]
    public function testArchive(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        // Act:
        $hall->archive();

        // Assert:
        $this->assertEquals(HallStatus::ARCHIVED, $hall->getStatus());
    }

    #[TestWith([HallStatus::ARCHIVED])]
    public function testCannotArchive(HallStatus $status): void
    {
        // Arrange:
        $hall = HallBuilder::reconstitute()
            ->withStatus($status)
            ->build();

        // Assert:
        $this->expectException(InvalidHallStatusException::class);

        // Act:
        $hall->archive();
    }
}
