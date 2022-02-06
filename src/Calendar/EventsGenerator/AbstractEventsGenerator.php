<?php

declare(strict_types=1);

namespace App\Calendar\EventsGenerator;

use App\Calendar\Dto\GenerateEventsDto;
use App\Calendar\Exception\AdminCalendarException;
use App\Calendar\Normalizer\EventSlot\NormalizerCollection;
use App\Calendar\Normalizer\WeekdayEventsTimeNormalizer;
use App\Entity\Event;
use App\Entity\Subject;
use App\Repository\EventRepository;
use App\Repository\SubjectRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class AbstractEventsGenerator
{
    private EventRepository $eventRepository;
    private EntityManagerInterface $entityManager;
    private NormalizerCollection $weekdayNormalizerCollection;
    protected SubjectRepository $subjectRepository;

    public function __construct(
        EventRepository        $eventRepository,
        EntityManagerInterface $entityManager,
        NormalizerCollection   $weekdayNormalizerCollection,
        SubjectRepository      $subjectRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
        $this->weekdayNormalizerCollection = $weekdayNormalizerCollection;
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * @throws Exception|AdminCalendarException
     */
    abstract public function generate(GenerateEventsDto $generateEventsDto): void;

    /**
     * @throws Exception
     */
    protected function generateWeekdayEvents(
        DateTimeImmutable $weekday,
        GenerateEventsDto $generateEventsDto,
        Subject $subject
    ): void {
        if (in_array($weekday->format('w'), [0, 6]) && !$generateEventsDto->includeWeekend()) {
            return;
        }

        $weekdayEventsNormalizer = $this->weekdayNormalizerCollection
            ->get($generateEventsDto->getEventDuration());
        $eventsStartDates = $weekdayEventsNormalizer->normalize($weekday, $generateEventsDto);

        foreach ($eventsStartDates as $eventStartDate) {
            if ($eventStartDate < new DateTimeImmutable()) {
                continue;
            }

            $event = $this->createEvent($eventStartDate, $subject, $generateEventsDto);

            $conflictedEvent = $this->eventRepository
                ->countOngoingSubjectEventsInPeriod($event->getStartAt(), $event->getEndAt(), $subject);

            if ($conflictedEvent) {
                continue;
            }

            $subject->addEvent($event);

            $this->entityManager->persist($subject);
            $this->entityManager->flush();
        }
    }

    private function createEvent(DateTimeImmutable $startAt, Subject $subject, GenerateEventsDto $generateEventsDto): Event
    {
        $event = new Event();
        $event->setStartAt($startAt);
        $event->setEndAt((clone $startAt)->add(
            new DateInterval(sprintf('PT%dM59S', $generateEventsDto->getEventDuration() - 1))
        ));
        $event->setSubject($subject);

        return $event;
    }
}
