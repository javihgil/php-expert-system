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

/**
 * Class KnowledgeBase
 */
class KnowledgeBase
{
    /**
     * @var Fact[]
     */
    protected $facts;

    /**
     * @var Rule[]
     */
    protected $rules;

    /**
     * @param Fact|Rule $item
     */
    public function add($item)
    {
        switch (get_class($item)) {
            case 'Jhg\ExpertSystem\Knowledge\Fact':
                $this->addFact($item);
                break;

            case 'Jhg\ExpertSystem\Knowledge\Rule':
                $this->addRule($item);
                break;

            default:
                throw new \RuntimeException('Invalid item');
        }
    }

    /**
     * @param Fact $fact
     */
    public function addFact(Fact $fact)
    {
        $this->facts[$fact->getName()] = $fact;
    }

    /**
     * @param Rule $rule
     */
    public function addRule(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @return Fact[]
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
    public function setFactsArray($facts)
    {
        foreach ($facts as $name => $value) {
            if (!isset($this->facts[$name])) {
                $this->addFact(Fact::factory($name, $value));
            } else {
                $this->facts[$name]->setValue($value);
            }
        }

    }
}