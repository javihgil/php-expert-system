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
 * Class NativePhpRuleExecutor
 */
class NativePhpRuleExecutor implements RuleExecutorInterface
{
    /**
     * @param Rule  $rule
     * @param array $facts
     *
     * @return bool|mixed
     */
    public function checkCondition(Rule $rule, array $facts)
    {
        /**
         * @param string $_action
         * @return array
         */
        $executor = function ($_action) use ($facts) {
            extract($facts);
            unset($facts);

            return eval($_action);
        };

        $code = trim($rule->getCondition());
        $code .= preg_match('/;$/i', $code) ? '' : ';';

        return $executor($code);
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