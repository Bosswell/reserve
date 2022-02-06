<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Exception;

class ValidationException extends Exception
{
    private array $violations = [];

    public function __construct(ConstraintViolationListInterface $violationList)
    {
        /** @var ConstraintViolationInterface $violation */
        foreach ($violationList as $violation) {
            $this->violations[] = [
                'message' => $violation->getMessage()
            ];
        }

        parent::__construct(sprintf('Validation error. %s', json_encode($this->violations)), 422);
    }

    public function getViolations(): array
    {
        return $this->violations;
    }
}