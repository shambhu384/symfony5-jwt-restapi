<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\RuleInterface;

class IsNumericRule implements RuleInterface
{
    public function apply($value)
    {
        return is_int($value);
    }
}
