<?php

declare(strict_types=1);

namespace App\Service;

interface RuleInterface
{
    public function apply($value);
}
