<?php

declare(strict_types=1);

namespace App\Calendar\Normalizer\EventSlot;

final class NormalizerCollection
{
    private iterable $normalizers;

    public function __construct(iterable $normalizers)
    {
        $this->normalizers = $normalizers;
    }

    public function get(int $eventDuration): NormalizerInterface
    {
        /** @var NormalizerInterface $normalizer */
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsDuration($eventDuration)) {
                return $normalizer;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Weekday events normalizer for %d minutes slots does not exist.', $eventDuration)
        );
    }
}
