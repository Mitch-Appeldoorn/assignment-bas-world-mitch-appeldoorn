<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\PaymentDatesInterface;
use App\DTO\MonthlyPaymentDTO;
use Carbon\Carbon;
use Carbon\CarbonInterface;

final class PaymentDatesService implements PaymentDatesInterface
{

    /**
     * @return array<MonthlyPaymentDTO>
     */
    public function getRemainingPayOutDates(MonthlyPaymentDTO|null $lastAddedMonth = null): array
    {
        $currentDate = Carbon::now();
        $remainingPayOutDates = [];
        if (!empty($lastAddedMonth)) {
            $lastAddedMonth = Carbon::parse($lastAddedMonth->monthName);
            if ($currentDate->month <= $lastAddedMonth->month) {
                return $remainingPayOutDates;
            }
        }
        for ($month = $currentDate->month; $month <= 12; $month++) {
            $baseSalaryPaymentDueDate = Carbon::create($currentDate->year, $month)->endOfMonth();
            if ($baseSalaryPaymentDueDate->isWeekend()) {
                $baseSalaryPaymentDueDate = $baseSalaryPaymentDueDate->previous(CarbonInterface::FRIDAY);
            }
            $bonusPayoutForPreviousMonthDate = Carbon::create($currentDate->year, $month)->dayOfMonth(15);
            if ($bonusPayoutForPreviousMonthDate->isWeekend()) {
                $bonusPayoutForPreviousMonthDate = $bonusPayoutForPreviousMonthDate->next(CarbonInterface::WEDNESDAY);
            }
            $remainingPayOutDates[] = new MonthlyPaymentDTO(
                monthName: $baseSalaryPaymentDueDate->monthName,
                baseSalaryPayoutDate: $baseSalaryPaymentDueDate->toImmutable(),
                previousMonthBonusPayoutDate: $bonusPayoutForPreviousMonthDate->toImmutable(),
            );
        }
        return $remainingPayOutDates;
    }
}
