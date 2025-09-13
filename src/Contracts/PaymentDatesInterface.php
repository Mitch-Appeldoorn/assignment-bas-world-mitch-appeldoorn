<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\MonthlyPaymentDTO;

interface PaymentDatesInterface
{
    /**
     * @return array<MonthlyPaymentDTO>
     */
    public function getRemainingPayOutDates(MonthlyPaymentDTO|null $lastAddedMonth = null): array;
}
