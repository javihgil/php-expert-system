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

use Jhg\ExpertSystem\Knowledge\RuleRunDecorator;

/**
 * Interface RuleExecutorInterface
 */
interface RuleExecutorInterface
{
    /**
     * @param RuleRunDecorator $rule
     * @param WorkingMemory    $workingMemory
     *
     * @return bool
     */
    public function checkCondition(RuleRunDecorator $rule, WorkingMemory $workingMemory);

    /**
     * @param RuleRunDecorator          $rule
     * @param WorkingMemory $workingMemory
     *
     * @return array
     */
    public function execute(RuleRunDecorator $rule, WorkingMemory $workingMemory);
}