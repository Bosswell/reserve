<?php

declare(strict_types=1);

namespace App\Calendar\Normalizer\EventSlot;

use App\Calendar\Dto\GenerateEventsDto;
use DatePeriod;
use DateTimeImmutable;

interface NormalizerInterface
{
    public function supportsDuration(int $eventDuration): bool;

    public function normalize(DateTimeImmutable $date, GenerateEventsDto $generateEventsDto): DatePeriod;
}
