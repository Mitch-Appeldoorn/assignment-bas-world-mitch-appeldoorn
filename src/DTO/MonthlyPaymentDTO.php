<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\CarbonImmutable;

final class MonthlyPaymentDTO
{
    public function __construct(
        public string $monthName,
        public CarbonImmutable $baseSalaryPayoutDate,
        public CarbonImmutable $previousMonthBonusPayoutDate,
    ) {
    }

    /**
     * @param array<string, string|null> $data
     * @return MonthlyPaymentDTO
     */
    public static function fromArray(array $data): MonthlyPaymentDTO
    {
        return new self(
            monthName: $data['monthName'],
            baseSalaryPayoutDate: CarbonImmutable::createFromFormat('Y-m-d', $data['baseSalaryPayOutDate']),
            previousMonthBonusPayoutDate: CarbonImmutable::createFromFormat(
                'Y-m-d',
                $data['previousMonthBonusPayOutDate']
            )
        );
    }
}
