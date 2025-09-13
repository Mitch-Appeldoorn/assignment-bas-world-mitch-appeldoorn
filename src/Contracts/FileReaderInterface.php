<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\MonthlyPaymentDTO;

interface FileReaderInterface
{
    public function getLastRowFromCsv(string $filePath): MonthlyPaymentDTO|null;
}
