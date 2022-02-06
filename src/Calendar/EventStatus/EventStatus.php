<?php

declare(strict_types=1);

namespace App\Calendar\EventStatus;

enum EventStatus: string
{
    case DURING = 'during';
    case FINISHED = 'finished';
    case NOT_STARTED = 'not_started';
}
