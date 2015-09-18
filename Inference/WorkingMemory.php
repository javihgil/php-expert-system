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

use Jhg\ExpertSystem\Knowledge\Fact;
use Jhg\ExpertSystem\Knowledge\Rule;

/**
 * Class WorkingMemory
 */
class WorkingMemory
{
    /**
     * @var Fact[]
     */
    protected $facts = [];

    /**
     * @param Fact $fact
     */
    public function setFact(Fact $fact)
    {
        $this->facts[$fact->getName()] = $fact;
    }

    /**
     * @param string $factName
     *
     * @return Fact
     */
    public function getFact($factName)
    {
        return $this->facts[$factName];
    }

    /**
     * @param string $factName
     */
    public function unsetFact($factName)
    {
        unset($this->facts[$factName]);
    }

    /**
     * @param Fact[] $facts
     */
    public function setAllFacts($facts)
    {
        /** @var Fact $fact */
        foreach ($facts as $fact) {
            $this->setFact($fact);
        }
    }

    /**
     * @return Fact[]
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
}