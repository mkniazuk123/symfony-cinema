<?php

namespace App\Tests\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\Reservations;
use App\Tests\Facilities\Fixtures\ReservationBuilder;
use PHPUnit\Framework\TestCase;

class ReservationsUnitTest extends TestCase
{
    public function testCannotBeCreatedWithDuplicates(): void
    {
        // Arrange:
        $items = [
            new ReservationBuilder()->withId('reservation1')->build(),
            new ReservationBuilder()->withId('reservation1')->build(),
        ];

        // Assert:
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Duplicate reservation "reservation1"');

        // Act:
        new Reservations($items);
    }

    public function testCannotAddDuplicate(): void
    {
        // Arrange:
        $items = [
            new ReservationBuilder()->withId('reservation1')->build(),
            new ReservationBuilder()->withId('reservation2')->build(),
        ];

        $duplicateItem = new ReservationBuilder()->withId('reservation1')->build();

        // Assert:
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Duplicate reservation "reservation1"');

        // Act:
        $reservations = new Reservations($items);
        $reservations->add($duplicateItem);
    }
}
