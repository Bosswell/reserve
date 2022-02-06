<?php

declare(strict_types=1);

namespace App\Calendar\EventsGenerator;

use App\Calendar\Dto\GenerateEventsDto;
use App\Calendar\Exception\AdminCalendarException;
use DateTimeImmutable;

class PeriodEventsGenerator extends AbstractEventsGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(GenerateEventsDto $generateEventsDto): void
    {
        $subject = $this->subjectRepository->find($generateEventsDto->getSubjectId());

        if (!$subject) {
            throw new AdminCalendarException('The subject has not been found.', 404);
        }

        /** @var DateTimeImmutable $weekday */
        foreach ($generateEventsDto->getDatePeriod() as $weekday) {
            $this->generateWeekdayEvents($weekday, $generateEventsDto, $subject);
        }
    }

    public static function getType(): string
    {
        return GenerateEventsDto::DATE_PERIOD_TYPE;
    }
}
