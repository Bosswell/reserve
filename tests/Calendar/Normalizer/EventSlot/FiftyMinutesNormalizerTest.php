<?php

declare(strict_types=1);

namespace App\Tests\Calendar\Normalizer\EventSlot;

use App\Calendar\Dto\GenerateEventsDto;
use App\Calendar\Normalizer\EventSlot\FiftyMinutesNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FiftyMinutesNormalizerTest extends TestCase
{
    public function testNormalizeEventsSlotsWithFloatedHourFromShouldBeRounded(): void
    {
        $normalizer = new FiftyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:02:00',
            'hourTo' => '12:00:00',
            'eventDuration' => 50,
        ]));
        
        $this->assertEquals($now->format('Y-m-d') . ' 10:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 11:40:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));

        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(2, $iterations);
    }

    public function testNormalizeEventsSlotsWithFloatedHourFromShouldBeRoundedInHoursBorder(): void
    {
        $normalizer = new FiftyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '10:55:00',
            'hourTo' => '12:00:00',
            'eventDuration' => 50,
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 11:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 11:50:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));

        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(1, $iterations);
    }

    public function testNormalizeEventsSlotsWithTimeBetweenHourFromAndHourToBiggerThenHourAndWithSomeMinutesShouldHaveProperDatePeriod(): void
    {
        $normalizer = new FiftyMinutesNormalizer();
        $now = new DateTimeImmutable();
        $datePeriod = $normalizer->normalize($now, new GenerateEventsDto([
            'hourFrom' => '8:00:00',
            'hourTo' => '10:30:00',
            'eventDuration' => 50,
        ]));

        $this->assertEquals($now->format('Y-m-d') . ' 08:00:00', $datePeriod->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($now->format('Y-m-d') . ' 10:30:00', $datePeriod->getEndDate()->format('Y-m-d H:i:s'));

        $iterations = 0;
        foreach ($datePeriod as $date) {
            $iterations++;
        }
        $this->assertEquals(3, $iterations);
    }
    
    public function testNormalizeEventsSlotsWithEventDurationDifferentThenFiftyMinutesShouldNotBeSupported(): void
    {
        $normalizer = new FiftyMinutesNormalizer();
        $this->assertFalse($normalizer->supportsDuration(12));
    }
}
