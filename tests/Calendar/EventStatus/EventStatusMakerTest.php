<?php

declare(strict_types=1);

namespace App\Tests\Calendar\EventStatus;

use App\Calendar\EventStatus\EventStatusMaker;
use App\Entity\Event;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class EventStatusMakerTest extends TestCase
{
    public function testNotStartedEventReturnNotStartedStatus(): void
    {
        $eventStatusMaker = new EventStatusMaker();
        $startAt = (new DateTimeImmutable())->modify('+1 day');
        $endAt = (new DateTimeImmutable())->modify('+2 day');

        $event = new Event();
        $event->setStartAt($startAt);
        $event->setEndAt($endAt);

        $status = $eventStatusMaker->make($event);
        $this->assertEquals('not_started', $status->value);
    }

    public function testStartedEventReturnDuringStatus(): void
    {
        $eventStatusMaker = new EventStatusMaker();
        $startAt = (new DateTimeImmutable())->modify('-10 minutes');
        $endAt = (new DateTimeImmutable())->modify('+10 minutes');

        $event = new Event();
        $event->setStartAt($startAt);
        $event->setEndAt($endAt);

        $status = $eventStatusMaker->make($event);
        $this->assertEquals('during', $status->value);
    }

    public function testFinishedEventReturnFinishedStatus(): void
    {
        $eventStatusMaker = new EventStatusMaker();
        $startAt = (new DateTimeImmutable())->modify('-10 minutes');
        $endAt = (new DateTimeImmutable())->modify('-40 minutes');

        $event = new Event();
        $event->setStartAt($startAt);
        $event->setEndAt($endAt);

        $status = $eventStatusMaker->make($event);
        $this->assertEquals('finished', $status->value);
    }
}
