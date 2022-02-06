<?php

declare(strict_types=1);

namespace App\Calendar\EventsGenerator;

use Symfony\Component\DependencyInjection\ServiceLocator;

class EventsGeneratorCollection
{
    private ServiceLocator $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function get(string $type): AbstractEventsGenerator
    {
        if ($this->serviceLocator->has($type)) {
            return $this->serviceLocator->get($type);
        }

        throw new \InvalidArgumentException(
            sprintf('Events generator with type [ %s ] does not exist.', $type)
        );
    }
}
