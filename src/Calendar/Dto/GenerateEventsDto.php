<?php

declare(strict_types=1);

namespace App\Calendar\Dto;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateEventsDto
{
    public const DATE_PERIOD_TYPE = 'period';
    public const DATE_SELECT_TYPE = 'select';

    /**
     * @Assert\NotEqualTo(
     *     value = 0,
     *     message = "The property [ subjectId ] was not specified."
     * )
     */
    private int $subjectId;

    /**
     * @Assert\NotBlank(
     *     message = "The property [ dateFrom ] was not specified.",
     *     groups = {"period"}
     * )
     * @Assert\Date(
     *     message = "The property [ dateFrom ] must be in format [ Y-m-d ].",
     *     groups = {"period"}
     * )
     */
    private string $dateFrom;

    /**
     * @Assert\NotBlank(
     *     message = "The property [ dateTo ] was not specified.",
     *     groups = {"period"}
     * )
     * @Assert\Date(
     *     message = "The property [ dateTo ] must be in format [ Y-m-d ].",
     *     groups = {"period"}
     * )
     */
    private string $dateTo;

    /**
     * @Assert\NotBlank(
     *     message = "The property [ hourFrom ] was not specified.",
     *     groups = {"period", "select"}
     * )
     * @Assert\Time(
     *     message = "The property [ hourFrom ] has invalid format. Use [ H:i:s ] format.",
     *     groups = {"period", "select"}
     * )
     * @Assert\NotEqualTo(
     *     message = "The property [ hourFrom ] must not be equal to [ hourTo ].",
     *     groups = {"period", "select"},
     *     propertyPath = "hourTo"
     * )
     */
    private string $hourFrom;

    /**
     * @Assert\NotBlank(
     *     message = "The property [ hourTo ] was not specified.",
     *     groups = {"period", "select"}
     * )
     * @Assert\Time(
     *     message = "The property [ hourTo ] has invalid format. Use [ H:i:s ] format.",
     *     groups = {"period", "select"}
     * )
     */
    private string $hourTo;

    private bool $includeWeekend;
    private string $eventGenerationFlow;

    /**
     * @Assert\NotBlank(
     *     message = "The property [ selectedDays ] was not specified.",
     *     groups = {"select"},
     * )
     * @Assert\All({
     *     @Assert\Date(
     *         message = "The value [ {{ value }} ] in the array with [ selectedDays ] is invalid. Use [ Y-m-d ] format."
     *     )
     * })
     */
    private array $selectedDays;

    /**
     * @Assert\Choice(
     *     message = "The property [ eventDuration ] has invalid value [ {{ value }} ]. Use [ {{ choices }} ].",
     *     choices = {30, 50},
     *     groups = {"period", "select"},
     * )
     */
    private int $eventDuration;

    public function __construct(array $data)
    {
        $this->subjectId = (int)($data['subjectId'] ?? 0);
        $this->eventGenerationFlow = $data['eventGenerationFlow'] ?? '';
        $this->selectedDays = (array)($data['selectedDays'] ?? []);
        $this->dateFrom = $data['dateFrom'] ?? '';
        $this->dateTo = $data['dateTo'] ?? '';
        $this->hourFrom = $data['hourFrom'] ?? '';
        $this->hourTo = $data['hourTo'] ?? '';
        $this->includeWeekend = (bool)($data['includeWeekend'] ?? false);
        $this->eventDuration = $data['eventDuration'] ?? 30;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function getDatePeriod(): DatePeriod
    {
        $dateTo = DateTimeImmutable::createFromFormat('Y-m-d', $this->dateTo);

        return new DatePeriod(
            DateTimeImmutable::createFromFormat('Y-m-d', $this->dateFrom),
            new DateInterval('P1D'),
            $dateTo->modify('+1 day')
        );
    }

    public function getHourFrom(): string
    {
        return $this->hourFrom;
    }

    public function getHourTo(): string
    {
        return $this->hourTo;
    }

    public function includeWeekend(): bool
    {
        return $this->includeWeekend;
    }

    public function getEventGenerationFlow(): string
    {
        return $this->eventGenerationFlow;
    }

    public function getSelectedDays(): array
    {
        return array_map(function ($selectedDay) {
            return DateTimeImmutable::createFromFormat('Y-m-d', $selectedDay);
        }, $this->selectedDays);
    }

    public function getEventDuration(): int
    {
        return $this->eventDuration;
    }
}
