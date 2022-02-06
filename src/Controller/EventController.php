<?php

namespace App\Controller;

use App\Calendar\CalendarFacade;
use App\Calendar\Dto\GenerateEventsDto;
use App\Calendar\Exception\AdminCalendarException;
use App\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    private CalendarFacade $calendarFacade;

    public function __construct(CalendarFacade $calendarFacade)
    {
        $this->calendarFacade = $calendarFacade;
    }

    #[Route('/admin/events/generate', name: 'generate_events')]
    #[ParamConverter('generateEventsDto', class: GenerateEventsDto::class, converter: 'dto_converter')]
    public function generateEvents(GenerateEventsDto $generateEventsDto): Response
    {
        try {
            $this->calendarFacade->generateEvents($generateEventsDto);

            return $this->json([
                'isOk' => true,
                'message' => 'Events slots has been generated.',
            ]);
        } catch (ValidationException|AdminCalendarException $ex) {
            return $this->json([
                'isOk' => false,
                'errors' => $ex instanceof ValidationException ? $ex->getViolations() : [['message' => $ex->getMessage()]],
            ]);
        }
    }
}
