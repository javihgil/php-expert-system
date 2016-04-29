<?php

/*
 * This file is part of the PhpExpertSystem package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\Inference;

use Jhg\ExpertSystem\Knowledge\Rule;

/**
 * Class WorkingMemory
 */
class WorkingMemory
{
    /**
     * @var array[]
     */
    protected $facts = [];

    /**
     * @param array[] $facts
     */
    public function setFacts(array $facts)
    {
        $this->facts = $facts;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setFact($name, $value)
    {
        $this->facts[$name] = $value;
    }

//    /**
//     * @param string $factName
//     *
//     * @return Fact
//     */
//    public function getFact($factName)
//    {
//        return $this->facts[$factName];
//    }
//
//    /**
//     * @param string $factName
//     */
//    public function unsetFact($factName)
//    {
//        unset($this->facts[$factName]);
//    }

    /**
     * @param array $facts
     */
    public function setAllFacts($facts)
    {
        $this->facts = $facts;
    }

    /**
     * @return array
     */
    public function getAllFacts()
    {
        return $this->facts;
    }

    /**
     * @var Rule[]
     */
    protected $executedRules = [];

    /**
     * @param Rule $rule
     *
     * @return bool
     */
    public function isExecuted(Rule $rule)
    {
        return in_array($rule, $this->executedRules);
    }

    /**
     * @param Rule $rule
     */
    public function setExecuted(Rule $rule)
    {
        $this->executedRules[] = $rule;
    }

    /**
     * @return \Jhg\ExpertSystem\Knowledge\Rule[]
     */
    public function getExecutedRules()
    {
        return $this->executedRules;
    }
}