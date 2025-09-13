<?php

declare(strict_types=1);

namespace App\DTO;

final class WriteFileDTO
{
    /**
     * @param list<MonthlyPaymentDTO> $addedPayments
     */
    public function __construct(
        public bool $writeStatus,
        public array $addedPayments = [],
        public string|null $errorMessage = null
    ) {
    }

}
