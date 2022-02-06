<?php

declare(strict_types=1);

namespace App\Tests\Calendar\EventsGenerator;

use App\Calendar\Dto\GenerateEventsDto;
use App\Calendar\EventsGenerator\DaySelectEventsGenerator;
use App\Calendar\Exception\AdminCalendarException;
use App\Entity\Subject;
use App\Repository\EventRepository;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class DaySelectEventsGeneratorTest extends KernelTestCase
{
    private DaySelectEventsGenerator $eventsGenerator;
    private EventRepository $eventRepository;
    private Subject $fakeSubject;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->eventsGenerator = $this->getContainer()->get(DaySelectEventsGenerator::class);
        /** @var SubjectRepository $subjectRepository */
        $subjectRepository = $this->getContainer()->get(SubjectRepository::class);
        $this->eventRepository = $this->getContainer()->get(EventRepository::class);
        $this->fakeSubject = $subjectRepository->findOneBy(['name' => 'company_trainer_1']);
    }

    public function testGenerateEventsForNotFoundConsultantThrowBackendApiException(): void
    {
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => 0,
        ]);

        $this->expectException(AdminCalendarException::class);
        $this->expectErrorMessage('The subject has not been found.');
        $this->expectExceptionCode(404);
        $this->eventsGenerator->generate($generateEventsDto);
    }

    public function testGenerateEventsWithIncludedWeekend(): void
    {
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '11:00:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
                '2049-01-27',
                '2049-01-28',
                '2049-01-29',
                '2049-01-30',
                '2049-01-31',
                '2049-02-01',
                '2049-02-02',
                '2049-02-03',
                '2049-02-04',
                '2049-02-05',
                '2049-02-06',
                '2049-02-07',
            ],
            'includeWeekend' => true,
        ]);

        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(0, $eventsInPeriod);

        $this->eventsGenerator->generate($generateEventsDto);
        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(2 * 7 * 6, $eventsInPeriod);
    }

    public function testGenerateEventsWithExcludedWeekend(): void
    {
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '11:00:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
                '2049-01-27',
                '2049-01-28',
                '2049-01-29',
                '2049-01-30',
                '2049-01-31',
                '2049-02-01',
                '2049-02-02',
                '2049-02-03',
                '2049-02-04',
                '2049-02-05',
                '2049-02-06',
                '2049-02-07',
            ],
            'includeWeekend' => false,
        ]);

        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(0, $eventsInPeriod);

        $this->eventsGenerator->generate($generateEventsDto);
        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(2 * 5 * 6, $eventsInPeriod);
    }

    public function testGenerateEventsInDateRangeWhereEventsAlreadyExists(): void
    {
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '9:00:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
            ],
            'includeWeekend' => false,
        ]);

        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-01-30')
        );

        $this->assertCount(0, $eventsInPeriod);

        $this->eventsGenerator->generate($generateEventsDto);
        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-01-30')
        );

        $this->assertCount(4, $eventsInPeriod);

        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '9:00:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
                '2049-01-27',
                '2049-01-28',
                '2049-01-29',
            ],
            'includeWeekend' => false,
        ]);

        $this->eventsGenerator->generate($generateEventsDto);
        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-01-30')
        );

        $this->assertCount(10, $eventsInPeriod);

        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-01-27')
        );
        $this->assertCount(4, $eventsInPeriod);
    }

    public function testGeneratingEventsOlderThenCurrentDayShouldNotBeCreated(): void
    {
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '11:00:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
                '2049-01-27',
                '2049-01-28',
                '2049-01-29',
                '2049-01-30',
            ],
            'includeWeekend' => true,
        ]);

        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(0, $eventsInPeriod);

        $this->eventsGenerator->generate($generateEventsDto);
        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2021-01-25'),
            new DateTimeImmutable('2021-02-08')
        );

        $this->assertCount(0, $eventsInPeriod);
    }

    public function testGeneratingEventsWithFiftyMinutesEventDurationShouldBeCreated(): void
    {
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->getContainer()->get(EventRepository::class);
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '11:00:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
            ],
            'includeWeekend' => false,
            'eventDuration' => 50,
        ]);

        $eventsInPeriod = $eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(0, $eventsInPeriod);

        $this->eventsGenerator->generate($generateEventsDto);
        $eventsInPeriod = $eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(6, $eventsInPeriod);
    }

    public function testGeneratingEventsWithFiftyMinutesEventDurationAndThirtyMinutesEventDurationShouldNotBeConflicted(): void
    {
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '10:30:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
            ],
            'includeWeekend' => false,
            'eventDuration' => 50,
        ]);

        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(0, $eventsInPeriod);

        $this->eventsGenerator->generate($generateEventsDto);
        $eventsInPeriod = $this->eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(6, $eventsInPeriod);

        /** @var EventRepository $eventRepository */
        $eventRepository = $this->getContainer()->get(EventRepository::class);
        $generateEventsDto = new GenerateEventsDto([
            'subjectId' => $this->fakeSubject->getId(),
            'hourFrom' => '8:00:00',
            'hourTo' => '10:30:00',
            'selectedDays' => [
                '2049-01-25',
                '2049-01-26',
            ],
            'includeWeekend' => false,
            'eventDuration' => 30,
        ]);
        $this->eventsGenerator->generate($generateEventsDto);

        $eventsInPeriod = $eventRepository->findBySubject(
            $this->fakeSubject,
            new DateTimeImmutable('2049-01-25'),
            new DateTimeImmutable('2049-02-08')
        );

        $this->assertCount(6, $eventsInPeriod);
    }
}
