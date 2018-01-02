<?php

declare(strict_types=1);

namespace Sergiors\Billing\Pimple\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Application;
use Sergiors\Billing\BillsCommand;

final class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['console'] = function (Container $container) {
            $console = new Application;
            $console->addCommands($container['commands']);

            return $console;
        };

        $container['commands'] = function (Container $container) {
            return [
                new BillsCommand($container['bills'] ?? []),
            ];
        };
    }
}

