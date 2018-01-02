<?php

declare(strict_types=1);

namespace Sergiors\Billing;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use DateTime;
use DateTimeImmutable;
use const Prelude\sum;
use function Sergiors\Billing\props;
use function Prelude\{pipe, map, filter, prop, contains};

final class BillsCommand extends Command
{
    private $bills = [];

    public function __construct( array $bills)
    {
        parent::__construct();
        $this->bills = $bills;
    }

    protected function configure()
    {
        $this->setName('bills')
            ->setDescription('List bills')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('tag', 't', InputOption::VALUE_OPTIONAL),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $headers = [
            'description',
            'amount',
            'precise_amount',
            'installments',
            'charges',
            'time',
        ];

        $fmttime = function (string $t) {
            return (new DateTimeImmutable($t))->format(DateTime::RFC822);
        };

        $fs = [
            map(function (array $bill) use ($fmttime) {
                return array_merge($bill, [
                    'time'           => $fmttime($bill['time']),
                    'precise_amount' => $preciseAmount = $bill['amount'] / 100,
                    'installments'   => $preciseAmount / $bill['charges'],
                ]);
            })
        ];

        if ($tag = $input->getOption('tag')) {
            $fs[] = filter(pipe(prop('tags'), contains($tag)));
        }

        $frows = array_merge($fs, [
            map(props($headers)),
        ]);

        $fpayments = array_merge($fs, [
            map(prop('installments')),
            sum,
        ]);

        $io = new SymfonyStyle($input, $output);
        $io->table(
            $headers,
            pipe(...$frows)($this->bills)
        );

        $io->text([
            'monthly_payments',
            pipe(...$fpayments)($this->bills)
        ]);
    }
}
