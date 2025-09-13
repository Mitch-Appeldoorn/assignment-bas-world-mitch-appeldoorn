<?php

declare(strict_types=1);

namespace App\Command;

use App\Contracts\FileReaderInterface;
use App\Contracts\PaymentDatesInterface;
use App\Contracts\WriteFileInterface;
use App\Service\WriteFileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use RuntimeException;

final class EmployeePayOutDateCommand extends Command
{
    public function __construct(
        public PaymentDatesInterface $paymentDatesInterface,
        public WriteFileInterface $writeFileInterface,
        public FileReaderInterface $fileReaderInterface,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:employee-payout-dates')
            ->setDescription('create/update employee pay out csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = new SymfonyStyle($input, $output);
        $filePath = $helper->ask(
            'Please enter the full path to an existing CSV file ' .
            'or the path where you would like the new CSV file to be created'
        );
        try {
            if (is_string($filePath)) {
                $filePath = str_ends_with($filePath, '.csv') ? $filePath : $filePath . '.csv';
                $lastPaymentMonth = $this->fileReaderInterface->getLastRowFromCsv(filePath: $filePath);
                $payOutDates = $this->paymentDatesInterface->getRemainingPayOutDates(lastAddedMonth: $lastPaymentMonth);
                if (!empty($payOutDates)) {
                    $table = new Table($output);
                    $table->setHeaders(WriteFileService::CSV_HEADER_ITEMS);
                    $output->writeln("<info> Adding employee pay out dates to file: {$filePath}</info>");
                    $addedPayments = $this->writeFileInterface->writeToCsv(
                        filePath: $filePath,
                        monthlyPaymentDates: $payOutDates
                    );
                    if (!$addedPayments->writeStatus) {
                        throw new RuntimeException("<error> {$lastPaymentMonth?->monthName} </error>");
                    }
                    $table->addRows($addedPayments->addedPayments);
                    $table->render();
                } else {
                    $output->writeln(
                        "<comment>File already synced until {$lastPaymentMonth?->monthName}</comment>"
                    );
                }
            } else {
                throw new RuntimeException('<error>Invalid file path</error>');
            }
        } catch (RuntimeException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
