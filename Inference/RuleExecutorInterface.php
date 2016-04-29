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
 * Interface RuleExecutorInterface
 */
interface RuleExecutorInterface
{
    /**
     * @param Rule  $rule
     * @param array $facts
     *
     * @return bool|mixed
     */
    public function checkCondition(Rule $rule, array $facts);

    /**
     * @param Rule  $rule
     * @param array $facts
     *
     * @return array
     */
    public function execute(Rule $rule, $facts);
}