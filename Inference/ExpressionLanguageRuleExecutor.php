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
     * @param Rule  $rule
     * @param array $facts
     *
     * @return bool|mixed
     */
    public function checkCondition(Rule $rule, array $facts)
    {
        try {
            return (bool) $this->expressionLanguage->evaluate($rule->getCondition(), $facts);
        } catch (SyntaxError $e) {
            return false;
        }
    }

    /**
     * @param Rule  $rule
     * @param array $facts
     *
     * @return array
     */
    public function execute(Rule $rule, $facts)
    {
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