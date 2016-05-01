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
 * Interface RuleExecutorInterface
 */
interface RuleExecutorInterface
{
    /**
     * @param Rule          $rule
     * @param WorkingMemory $workingMemory
     *
     * @return bool
     */
    public function checkCondition(Rule $rule, WorkingMemory $workingMemory);

    /**
     * @param Rule          $rule
     * @param WorkingMemory $workingMemory
     *
     * @return array
     */
    public function execute(Rule $rule, WorkingMemory $workingMemory);
}