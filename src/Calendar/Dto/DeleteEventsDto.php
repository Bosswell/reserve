<?php

declare(strict_types=1);

namespace App\Calendar\Dto;

use Datetime;
use Symfony\Component\Validator\Constraints as Assert;

final class DeleteEventsDto
{
    /**
     * @Assert\NotEqualTo(
     *     value = 0,
     *     message = "The property [ subjectId ] was not specified."
     * )
     */
    private int $subjectId;

    /**
     * @Assert\NotBlank(
     *     message = "The property [ dateFrom ] was not specified."
     * )
     * @Assert\DateTime(
     *     message = "The property [ dateFrom ] must be in format [ Y-m-d_H:i:s ]."
     * )
     */
    private string $dateFrom;

    /**
     * @Assert\NotBlank(
     *     message = "The property [ dateTo ] was not specified."
     * )
     * @Assert\DateTime(
     *     message = "The property [ dateTo ] must be in format [ Y-m-d_H:i:s ]."
     * )
     */
    private string $dateTo;

    public function __construct(array $data)
    {
        $this->subjectId = (int)($data['subjectId'] ?? 0);
        $this->dateFrom = $data['dateFrom'] ?? '';
        $this->dateTo = $data['dateTo'] ?? '';
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function getDateFrom(): Datetime
    {
        return DateTime::createFromFormat('Y-m-d_H:i:s', $this->dateFrom);
    }

    public function getDateTo(): Datetime
    {
        return DateTime::createFromFormat('Y-m-d_H:i:s', $this->dateTo);
    }
}
