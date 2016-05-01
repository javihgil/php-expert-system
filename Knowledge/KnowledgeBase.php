<?php

/*
 * This file is part of the PhpExpertSystem package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\Knowledge;

use Jhg\ExpertSystem\Rule\Rule;

/**
 * Class KnowledgeBase
 */
class KnowledgeBase
{
    /**
     * @var array[]
     */
    protected $facts = [];

    /**
     * @var Rule[]
     */
    protected $rules = [];

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addFact($name, $value)
    {
        $this->facts[$name] = $value;
    }

    /**
     * @param Rule $rule
     */
    public function addRule(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @return array[]
     */
    public function getFacts()
    {
        return $this->facts;
    }

    /**
     * @return Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $facts
     */
    public function setFacts($facts)
    {
        $this->facts = $facts;
    }
}