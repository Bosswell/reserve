<?php

declare(strict_types=1);

namespace App\Calendar\EventStatus;

use App\Entity\Event;

final class EventStatusMaker
{
    public function make(Event $event): EventStatus
    {
        $eventStart = $event->getStartAt();
        $eventEnd = $event->getEndAt();
        $now = new \DateTime();

        if ($now >= $eventStart && $now <= $eventEnd) {
            return EventStatus::DURING;
        }

        if ($eventStart < $now) {
            return EventStatus::FINISHED;
        }

        return EventStatus::NOT_STARTED;
    }
}
