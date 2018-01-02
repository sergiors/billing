<?php

declare(strict_types=1);

namespace Sergiors\Billing;

use Closure;

function props(array $ks): Closure
{
    return function (array $xs) use ($ks) {
        return array_map(function ($k) use ($xs) {
            return $xs[$k] ?? null;
        }, $ks);
    };
}
