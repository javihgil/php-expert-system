<?php

/*
 * This file is part of the PhpExpertSystem package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\Rule;

use Jhg\ExpertSystem\Inference\InferenceProfiler;
use Jhg\ExpertSystem\Inference\InferenceProfilerAwareInterface;
use Jhg\ExpertSystem\Inference\WorkingMemory;

/**
 * Class NativePhpRuleExecutor
 */
class NativePhpRuleExecutor implements RuleExecutorInterface, InferenceProfilerAwareInterface
{
    /**
     * @var InferenceProfiler
     */
    protected $inferenceProfiler;

    /**
     * @param InferenceProfiler $inferenceProfiler
     */
    public function setInferenceProfiler(InferenceProfiler $inferenceProfiler)
    {
        $this->inferenceProfiler = $inferenceProfiler;
    }

    /**
     * @param Rule          $rule
     * @param WorkingMemory $workingMemory
     *
     * @return bool
     */
    private function execCondition(Rule $rule, WorkingMemory $workingMemory)
    {
        $facts = $workingMemory->getAllFacts();

        /**
         * @param string $_action
         * @return array
         */
        $executor = function ($_action) use ($facts) {
            extract($facts);
            unset($facts);

            $_return = eval($_action);

            return $_return;
        };

        $code = trim($rule->getCondition());
        $code = (preg_match('/^return /i', $code) ? '' : 'return '). $code;
        $code = $code . (preg_match('/;$/i', $code) ? '' : ';');

        return $executor($code);
    }

    /**
     * @param Rule          $rule
     * @param WorkingMemory $workingMemory
     *
     * @return bool|mixed
     */
    public function checkCondition(Rule $rule, WorkingMemory $workingMemory)
    {
        if ($rule instanceof RuleRunDecorator && $rule->hasConditionWildcards()) {
            $this->inferenceProfiler && $this->inferenceProfiler->setMatchingRuleCheckConditionWildcard();

            $combinations = $rule->getWildcardCombinationRules();

            if (!$combinations) {
                return false;
            }

            foreach ($combinations as $combinationRule) {
                $this->inferenceProfiler && $this->inferenceProfiler->addMatchingRuleCheckConditionWildcardCombinationCheck($combinationRule);

                if ($this->execCondition($combinationRule, $workingMemory)) {
                    $rule->addSuccessCombinationRule($combinationRule);

                    $this->inferenceProfiler && $this->inferenceProfiler->setMatchingRuleCheckConditionWildcardCombinationCheckSuccess();
                }
            }

            return $rule->hasSuccessCombinationRules();
        } else {
            return $this->execCondition($rule, $workingMemory);
        }
    }

    /**
     * @param Rule          $rule
     * @param WorkingMemory $workingMemory
     *
     * @return array
     */
    private function execAction(Rule $rule, WorkingMemory $workingMemory)
    {
        $facts = $workingMemory->getAllFacts();

        /**
         * @param string $_action
         * @return array
         */
        $executor = function ($_action) use ($facts) {
            extract($facts);
            unset($facts);

            eval($_action);
            unset($_action);

            return get_defined_vars();
        };

        $code = trim($rule->getAction());
        $code = $code . (preg_match('/;$/i', $code) ? '' : ';');

        return $executor($code);
    }

    /**
     * @param Rule          $rule
     * @param WorkingMemory $workingMemory
     *
     * @return array
     */
    public function execute(Rule $rule, WorkingMemory $workingMemory)
    {
        if ($rule instanceof RuleRunDecorator && $rule->hasSuccessCombinationRules()) {
            $workingMemory->setExecuted($rule->getRule());
            foreach ($rule->getSuccessCombinationRules() as $combinationRule) {
                $this->inferenceProfiler && $this->inferenceProfiler->addIterationRuleExecution($combinationRule);

                $workingMemory->setAllFacts($this->execAction($combinationRule, $workingMemory));
                $workingMemory->setExecuted($combinationRule);
            }
        } else {
            $this->inferenceProfiler && $this->inferenceProfiler->addIterationRuleExecution($rule);
            $workingMemory->setAllFacts($this->execAction($rule, $workingMemory));
            $workingMemory->setExecuted($rule->getRule());
        }
    }
}