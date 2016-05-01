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
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

/**
 * Class ExpressionLanguageRuleExecutor
 */
class ExpressionLanguageRuleExecutor implements RuleExecutorInterface
{
    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(ExpressionLanguage $expressionLanguage = null)
    {
        $this->expressionLanguage = $expressionLanguage ? : new ExpressionLanguage();
    }

    /**
     * @param RuleRunDecorator $rule
     * @param WorkingMemory    $workingMemory
     *
     * @return bool
     */
    public function checkCondition(RuleRunDecorator $rule, WorkingMemory $workingMemory)
    {
        try {
            return (bool) $this->expressionLanguage->evaluate($rule->getCondition(), $workingMemory->getAllFacts());
        } catch (SyntaxError $e) {
            return false;
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

        return $executor($rule->getAction());
    }
}