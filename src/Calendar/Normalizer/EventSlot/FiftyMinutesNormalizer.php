<?php

declare(strict_types=1);

namespace App\Calendar\Normalizer\EventSlot;

use App\Calendar\Dto\GenerateEventsDto;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;

class FiftyMinutesNormalizer implements NormalizerInterface
{
    public const EVENT_DURATION = 50;

    public function supportsDuration(int $eventDuration): bool
    {
        return $eventDuration === self::EVENT_DURATION;
    }

    public function normalize(DateTimeImmutable $date, GenerateEventsDto $generateEventsDto): DatePeriod
    {
        $dateFrom = DateTimeImmutable::createFromFormat(
            'Y-m-d G:i:s',
            $date->format('Y-m-d ') . $this->normalizeHourFrom($generateEventsDto->getHourFrom())
        );
        $dateTo = DateTimeImmutable::createFromFormat(
            'Y-m-d G:i:s',
            $date->format('Y-m-d ') . $this->normalizeHourTo($generateEventsDto->getHourTo())
        );

        $interval = $dateFrom->diff($dateTo);
        $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        $slotCount = (int)floor($minutes / self::EVENT_DURATION);

        return new DatePeriod(
            $dateFrom,
            new DateInterval('PT50M'),
            (clone $dateFrom)->modify(sprintf('+%d minutes', $slotCount * self::EVENT_DURATION))
        );
    }

    private function normalizeHourFrom(string $hourFrom): string
    {
        [$hours, $minutes] = array_map('intval', explode(':', $hourFrom));

        if ($minutes > 54) {
            ++$hours;
            $minutes = 0;
        } else {
            $minutes = round($minutes / 10) * 10;
        }

        return sprintf('%s:%s:00', $hours, $minutes < 10 ? '0' . $minutes : $minutes);
    }

    private function normalizeHourTo(string $hourTo): string
    {
        [$hours, $minutes] = array_map('intval', explode(':', $hourTo));

        return sprintf('%s:%s:00', $hours, $minutes < 10 ? '0' . $minutes : $minutes);
    }
}
