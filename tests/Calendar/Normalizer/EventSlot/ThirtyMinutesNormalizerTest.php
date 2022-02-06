<?php

declare(strict_types=1);

namespace App\Tests\Calendar\Normalizer\EventSlot;

use App\Calendar\Dto\GenerateEventsDto;
use App\Calendar\Normalizer\EventSlot\FiftyMinutesNormalizer;
use App\Calendar\Normalizer\EventSlot\ThirtyMinutesNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ThirtyMinutesNormalizerTest extends TestCase
{
    public function testNormalizeEventsSlotsWithHourFromPropertyLessThenFullHourButLongerThenHalfHourAndHourToPropertySameAsHourFrom(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:42:00',
            'hourTo' => '11:00:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(0, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourFromPropertyLessThenFullHourButLongerThenHalfHour(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:42:00',
            'hourTo' => '12:00:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 12:00:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $this->assertEquals($iterations * 60 * 30 + $datePeriod->getStartDate()->getTimestamp(), $date->getTimestamp());
            $iterations++;
        }
        $this->assertEquals(2, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourFromPropertyLongerThenFullHourButLessThenHalfHourAndHourToPropertySameAsHourFrom(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:12:00',
            'hourTo' => '10:30:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 10:30:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 10:30:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(0, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourFromPropertyLongerThenFullHourButLessThenHalfHour(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:12:00',
            'hourTo' => '11:00:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 10:30:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $this->assertEquals($iterations * 60 * 30 + $datePeriod->getStartDate()->getTimestamp(), $date->getTimestamp());
            $iterations++;
        }
        $this->assertEquals(1, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourToPropertyLessThenFullHourButLongerThenHalfHourAndHourFromPropertySameAsHourTo(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '11:30:00',
            'hourTo' => '11:40:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 11:30:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 11:30:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(0, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourToPropertyLessThenFullHourButLongerThenHalfHour(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '11:30:00',
            'hourTo' => '12:40:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 11:30:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 12:30:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $this->assertEquals($iterations * 60 * 30 + $datePeriod->getStartDate()->getTimestamp(), $date->getTimestamp());
            $iterations++;
        }
        $this->assertEquals(2, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourToPropertyLessThenHalfHourButLongerThenFullHourAndHourFromPropertySameAsHourTo(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:00:00',
            'hourTo' => '10:20:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 10:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 10:00:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(0, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourToPropertyLessThenHalfHourButLongerThenFullHour(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:00:00',
            'hourTo' => '11:20:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 10:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $this->assertEquals($iterations * 60 * 30 + $datePeriod->getStartDate()->getTimestamp(), $date->getTimestamp());
            $iterations++;
        }
        $this->assertEquals(2, $iterations);
    }

    public function testNormalizeEventsSlotsWithHourFromValueEqualToHourToValueShouldHaveNoIterations(): void
    {
        $normalizer = new ThirtyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '11:00:00',
            'hourTo' => '11:00:00',
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));
        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(0, $iterations);
    }

    public function testNormalizeEventsSlotsWithEventDurationDifferentThenFiftyMinutesShouldNotBeSupported(): void
    {
        $normalizer = new FiftyMinutesNormalizer();
        $this->assertFalse($normalizer->supportsDuration(12));
    }
}
