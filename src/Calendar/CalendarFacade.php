<?php

namespace App\Calendar;

use App\Calendar\Dto\GenerateEventsDto;
use App\Calendar\EventsGenerator\EventsGeneratorCollection;
use App\Calendar\Exception\AdminCalendarException;
use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CalendarFacade
{
    private EventsGeneratorCollection $eventsGeneratorCollection;
    private ValidatorInterface $validator;

    public function __construct(EventsGeneratorCollection $eventsGeneratorCollection, ValidatorInterface $validator)
    {
        $this->eventsGeneratorCollection = $eventsGeneratorCollection;
        $this->validator = $validator;
    }

    /**
     * @throws ValidationException|AdminCalendarException
     */
    public function generateEvents(GenerateEventsDto $generateEventsDto): void
    {
        $violations = $this->validator->validate($generateEventsDto, null, $generateEventsDto->getEventGenerationFlow());

        if ($violations->count() !== 0) {
            throw new ValidationException($violations);
        }

        $eventsGenerator = $this->eventsGeneratorCollection->get(
            $generateEventsDto->getEventGenerationFlow()
        );
        $eventsGenerator->generate($generateEventsDto);
    }
}