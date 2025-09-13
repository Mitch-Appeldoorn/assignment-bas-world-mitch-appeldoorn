<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\MonthlyPaymentDTO;
use App\DTO\WriteFileDTO;

interface WriteFileInterface
{

    /**
     * @param array<MonthlyPaymentDTO> $monthlyPaymentDates
     */
    public function writeToCsv(string $filePath, array $monthlyPaymentDates): WriteFileDTO;
}
