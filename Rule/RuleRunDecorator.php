<?php

namespace Jhg\ExpertSystem\Rule;

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

        if ($this->hasConditionWildcards()) {
            $this->calculateWildcardCombinations();
        }
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

    /**
     * Wildcard combination rules
     *
     * @var array
     */
    protected $wildcardCombinationRules;

    /**
     * Calculate possible wildcards
     */
    protected function calculateWildcardCombinations()
    {
        $wildCards = $this->getConditionWildcards();

        if (sizeof($wildCards)>2) {
            throw new \Exception('More than 2 wildcards is not yet supported');
        }

        $combinations = $this->combinations($this->workingMemory->getAllFacts(), sizeof($wildCards));

        foreach ($combinations as $combination) {
            $proccesedCondition = $this->parseCode($this->getCondition(), $this->getConditionWildcards(), $combination);
            $proccesedAction = $this->parseCode($this->getAction(), $this->getConditionWildcards(), $combination);
            $rule = Rule::factory($proccesedCondition, $proccesedAction, $this->getPriority());

            $this->wildcardCombinationRules[] = $rule;
        }
    }

    /**
     * @param string $code
     * @param array  $wildcards
     * @param array  $combination
     * 
     * @return string
     */
    private function parseCode($code, array $wildcards, array $combination)
    {
        return str_ireplace($wildcards, $combination, $code);
    }

    /**
     * @param array $elements
     * @param int   $number
     *
     * @return array
     */
    private function combinations(array $elements, $number)
    {
        $permutations = [];

        foreach ($elements as $i1 => $element1) {
            if ($number > 1) {
                foreach ($elements as $i2 => $element2) {
                    if ($element1 != $element2) {
                        $permutations[] = ['$'.$i1, '$'.$i2];
                    }
                }
            } else {
                $permutations[] = ['$'.$i1];
            }
        }

        return $permutations;
    }

    /**
     * @return array
     */
    public function getWildcardCombinationRules()
    {
        return $this->wildcardCombinationRules;
    }

    /**
     * @var Rule[]
     */
    protected $successCombinationRules = [];

    /**
     * @param Rule $combinationRule
     */
    public function addSuccessCombinationRule(Rule $combinationRule)
    {
        $this->successCombinationRules[] = $combinationRule;
    }

    /**
     * @return Rule[]
     */
    public function getSuccessCombinationRules()
    {
        return $this->successCombinationRules;
    }

    /**
     * @return bool
     */
    public function hasSuccessCombinationRules()
    {
        return (bool) sizeof($this->getSuccessCombinationRules());
    }
}