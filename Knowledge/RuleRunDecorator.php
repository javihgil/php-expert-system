<?php

namespace Jhg\ExpertSystem\Knowledge;

use Jhg\ExpertSystem\Inference\WorkingMemory;

/**
 * Class RuleRunDecorator
 */
class RuleRunDecorator extends Rule
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var WorkingMemory
     */
    protected $workingMemory;

    /**
     * RuleRunDecorator constructor.
     *
     * @param Rule          $rule
     * @param WorkingMemory $workingMemory
     */
    public function __construct(Rule $rule, WorkingMemory $workingMemory)
    {
        $this->rule = $rule;
        $this->workingMemory = $workingMemory;
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->rule->getCondition();
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->rule->getAction();
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->rule->getPriority();
    }

    /**
     * @return bool
     */
    public function hasConditionWildcards()
    {
        return $this->rule->hasConditionWildcards();
    }

    /**
     * @return array
     */
    public function getConditionWildcards()
    {
        return $this->rule->getConditionWildcards();
    }
}