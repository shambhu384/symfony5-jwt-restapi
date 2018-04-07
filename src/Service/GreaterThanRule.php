<?php

namespace App\Service;

class GreaterThanRule implements RuleInterface
{
    public function apply($value)
    {
        return $value > 4000;
    }
}

