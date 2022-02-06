<?php

declare(strict_types=1);

namespace App\Calendar\Normalizer\EventSlot;

use App\Calendar\Dto\GenerateEventsDto;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;

class ThirtyMinutesNormalizer implements NormalizerInterface
{
    public const EVENT_DURATION = 30;

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
        $interval = new DateInterval('PT30M');

        return new DatePeriod($dateFrom, $interval, $dateTo);
    }

    private function normalizeHourFrom(string $hourFrom): string
    {
        [$hours, $minutes] = array_map('intval', explode(':', $hourFrom));

        if (!in_array($minutes, [0, 30])) {
            if ($minutes > 30) {
                ++$hours;
                $minutes = 0;
            } elseif ($minutes > 0) {
                $minutes = 30;
            }
        }

        return sprintf('%s:%s:00', $hours, $minutes < 10 ? '0' . $minutes : $minutes);
    }

    private function normalizeHourTo(string $hourTo): string
    {
        [$hours, $minutes] = array_map('intval', explode(':', $hourTo));

        if (!in_array($minutes, [0, 30])) {
            if ($minutes > 30) {
                $minutes = 30;
            } elseif ($minutes > 0) {
                $minutes = 0;
            }
        }

        return sprintf('%s:%s:00', $hours, $minutes < 10 ? '0' . $minutes : $minutes);
    }
}
