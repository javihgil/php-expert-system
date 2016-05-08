<?php

/*
 * This file is part of the life-project package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\Inference;

use Jhg\ExpertSystem\Rule\Rule;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class InferenceProfiler
 */
class InferenceProfiler
{
    /**
     * @var array
     */
    protected $inferences;

    /**
     * @var int
     */
    protected $currentInference = null;

    /**
     * @var int
     */
    protected $currentIteration = null;

    /**
     * @var int
     */
    protected $currentMatchingRuleCheck = null;

    /**
     * @var int
     */
    protected $currentMatchingRuleCheckConditionWildcard = null;

    /**
     * @var int
     */
    protected $currentIterationExecution = null;

    /**
     * ProfileWorkingMemory constructor.
     */
    public function __construct()
    {
        $this->inferences = [];
    }

    /**
     * Finish inference
     *
     * @param array|null $facts
     */
    public function startInference(array $facts = null)
    {
        // ends previous inference if present
        $this->endInference();

        $this->currentInference = sizeof($this->inferences);
        $this->currentIteration = null;
        $this->currentMatchingRuleCheck = null;
        $this->currentMatchingRuleCheckConditionWildcard = null;
        $this->currentIterationExecution = null;

        $this->inferences[$this->currentInference] = [
            'active' => true,
            'iterations' => [],
            'previousFacts' => $facts,
            'newFacts' => null,
            'stopwatch' => new Stopwatch(),
        ];
    }

    /**
     * Finish inference
     *
     * @param array|null $facts
     */
    public function endInference(array $facts = null)
    {
        if ($this->currentInference !== null && $this->inferences[$this->currentInference]['active']) {
            $this->inferences[$this->currentInference]['active'] = false;

            if ($this->inferences[$this->currentInference]['previousFacts']!== null && $facts !== null) {
                $this->inferences[$this->currentInference]['newFacts'] = [] ; //array_diff($facts, $this->inferences[$this->currentInference]['previousFacts']);
            }

            $this->endIteration();
        }
    }

    /**
     * @return array
     */
    public function getInferences()
    {
        return $this->inferences;
    }

    /**
     * Creates a new iteration
     */
    public function startIteration()
    {
        $this->endIteration();

        $this->currentIteration = sizeof($this->inferences[$this->currentInference]['iterations']);
        $this->currentMatchingRuleCheck = 0;
        $this->currentMatchingRuleCheckConditionWildcard = 0;
        $this->currentIterationExecution = 0;

        $this->inferences[$this->currentInference]['stopwatch']->openSection();

        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration] = [
            'matchingRuleChecks' => [],
            'selectedRule' => [],
            'executions' => [],
            'sectionEvents' => null,
        ];
    }

    /**
     *
     */
    public function startMatchingRules()
    {
        $this->inferences[$this->currentInference]['stopwatch']->start('start-matching-rules');
    }

    /**
     *
     */
    public function endMatchingRules()
    {
        $this->inferences[$this->currentInference]['stopwatch']->start('end-matching-rules');
    }

    /**
     *
     */
    public function endIteration()
    {
        if ($this->currentInference !== null && $this->currentIteration !== null) {
            $this->inferences[$this->currentInference]['stopwatch']->stopSection('iteration#'.$this->currentIteration);
            $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['sectionEvents'] = $this->inferences[$this->currentInference]['stopwatch']->getSectionEvents('iteration#'.$this->currentIteration);
        }
    }

    /**
     * @param Rule   $rule
     * @param string $checkResult
     */
    public function addMatchingRuleCheck(Rule $rule, $checkResult = '')
    {
        $this->currentMatchingRuleCheck = sizeof($this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['matchingRuleChecks']);
        $this->currentMatchingRuleCheckConditionWildcard = 0;

        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['matchingRuleChecks'][$this->currentMatchingRuleCheck] = [
            'name' => $rule->getName(),
            'condition' => $rule->getCondition(),
            'wildcard' => false,
            'result' => $checkResult,
        ];
    }

    /**
     * @param string $result
     */
    public function setMatchingRuleCheckResult($result)
    {
        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['matchingRuleChecks'][$this->currentMatchingRuleCheck]['result'] = $result;
    }

    /**
     *
     */
    public function setMatchingRuleCheckConditionWildcard()
    {
        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['matchingRuleChecks'][$this->currentMatchingRuleCheck]['wildcard'] = [];
    }

    /**
     * @param Rule $combinationRule
     */
    public function addMatchingRuleCheckConditionWildcardCombinationCheck(Rule $combinationRule)
    {
        $this->currentMatchingRuleCheckConditionWildcard = sizeof($this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['matchingRuleChecks'][$this->currentMatchingRuleCheck]['wildcard']);

        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['matchingRuleChecks'][$this->currentMatchingRuleCheck]['wildcard'][$this->currentMatchingRuleCheckConditionWildcard] = [
            'name' => $combinationRule->getName(),
            'condition' => $combinationRule->getCondition(),
            'result' => '',
        ];
    }

    /**
     *
     */
    public function setMatchingRuleCheckConditionWildcardCombinationCheckSuccess()
    {
        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['matchingRuleChecks'][$this->currentMatchingRuleCheck]['wildcard'][$this->currentMatchingRuleCheckConditionWildcard]['result'] = 'success';
    }


    /**
     * @param Rule   $rule
     * @param string $reason
     */
    public function setIterationSelectedRule(Rule $rule, $reason = '')
    {
        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['selectedRule'] = [
            'name' => $rule->getName(),
            'action' => $rule->getAction(),
            'reason' => $reason,
        ];
    }

    /**
     * @param Rule $rule
     */
    public function addIterationRuleExecution(Rule $rule)
    {
        $this->currentIterationExecution = sizeof($this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['executions']);

        $this->inferences[$this->currentInference]['iterations'][$this->currentIteration]['executions'][$this->currentIterationExecution] = [
            'name' => $rule->getName(),
            'action' => $rule->getAction(),
        ];
    }
}
