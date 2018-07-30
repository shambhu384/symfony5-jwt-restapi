<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\RuleInterface;

class RuleManager
{
    private $rules = [];

    /**
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule)
    {
        $this->rules[] = $rule;
    }

    public function applyRules(array $data)
    {
        /*if(count($this->rules) == 0) {
            throw new \exception('Please add roles');
        }*/
        foreach ($this->rules as $rule) {
            $data = array_filter($data, function ($value) use ($rule) {
                return $rule->apply($value);
            });
        }

        return $data;
    }
}
