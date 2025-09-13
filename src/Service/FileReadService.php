<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\FileReaderInterface;
use App\DTO\MonthlyPaymentDTO;

final class FileReadService implements FileReaderInterface
{

    public function getLastRowFromCsv(string $filePath): MonthlyPaymentDTO|null
    {
        if (!is_readable($filePath)) {
            return null;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines || count($lines) < 2) {
            return null;
        }
        $header = str_getcsv($lines[0]);
        $lastRow = str_getcsv(end($lines));
        $data = array_combine($header, $lastRow);

        return MonthlyPaymentDTO::fromArray($data);
    }
}
