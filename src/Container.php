<?php

declare(strict_types=1);

namespace Sergiors\Billing;

abstract class Container extends \Pimple\Container
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);
        
        $this->initializeProviders();
    }

    private function initializeProviders(): void
    {
        $providers = $this->registerProviders();

        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }

    abstract public function registerProviders(): array;
}
