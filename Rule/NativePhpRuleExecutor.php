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

use Jhg\ExpertSystem\Inference\WorkingMemory;

/**
 * Class NativePhpRuleExecutor
 */
class NativePhpRuleExecutor implements RuleExecutorInterface
{
    /**
     * @param RuleRunDecorator $rule
     * @param WorkingMemory    $workingMemory
     *
     * @return bool
     */
    protected function execCondition(RuleRunDecorator $rule, WorkingMemory $workingMemory)
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
     * @param RuleRunDecorator $rule
     * @param WorkingMemory    $workingMemory
     *
     * @return bool|mixed
     */
    public function checkCondition(RuleRunDecorator $rule, WorkingMemory $workingMemory)
    {
        if ($rule->hasConditionWildcards()) {

        } else {
            return $this->execCondition($rule, $workingMemory);
        }
    }

    /**
     * @param RuleRunDecorator $rule
     * @param WorkingMemory    $workingMemory
     *
     * @return array
     */
    public function execute(RuleRunDecorator $rule, WorkingMemory $workingMemory)
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
}