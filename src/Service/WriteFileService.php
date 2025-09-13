<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\FileReaderInterface;
use App\Contracts\WriteFileInterface;
use App\DTO\MonthlyPaymentDTO;
use App\DTO\WriteFileDTO;
use RuntimeException;

final class WriteFileService implements WriteFileInterface
{
    public const CSV_HEADER_ITEMS = ['monthName', 'baseSalaryPayOutDate', 'previousMonthBonusPayOutDate'];

    public function __construct(private readonly FileReaderInterface $fileReader)
    {
    }

    /**
     * @param array<MonthlyPaymentDTO> $monthlyPaymentDates
     */
    public function writeToCsv(string $filePath, array $monthlyPaymentDates): WriteFileDTO
    {
        $lastAddedMonth = $this->fileReader->getLastRowFromCsv($filePath);
        $payOutFile = fopen($filePath, "a+");
        try {
            if (!$lastAddedMonth) {
                if (!fputcsv($payOutFile, self::CSV_HEADER_ITEMS)) {
                    throw new RuntimeException('Unable to write headers to CSV file');
                }
            }
            $addedRows = [];
            foreach ($monthlyPaymentDates as $payOutDate) {
                $row = [
                    $payOutDate->monthName,
                    $payOutDate->baseSalaryPayoutDate->format('Y-m-d'),
                    $payOutDate->previousMonthBonusPayoutDate->format('Y-m-d'),
                ];
                if (!fputcsv($payOutFile, $row)) {
                    throw new RuntimeException('Could not write pay out dates to file');
                }
                $addedRows[] = $row;
            }
        } catch (RuntimeException $runtimeException) {
            return new WriteFileDTO(
                writeStatus: false,
                addedPayments: [],
                errorMessage: $runtimeException->getMessage()
            );
        } finally {
            if (is_resource($payOutFile)) {
                fclose($payOutFile);
            }
        }
        return new WriteFileDTO(
            writeStatus: true,
            addedPayments: $addedRows
        );
    }
}
