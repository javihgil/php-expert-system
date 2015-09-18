<?php

/*
 * This file is part of the PhpExpertSystem package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\ConflictResolution;

use Jhg\ExpertSystem\Inference\WorkingMemory;
use Jhg\ExpertSystem\Knowledge\Rule;

/**
 * Interface StrategyInterface
 */
interface StrategyInterface
{
    /**
     * @param Rule[]        $rules
     * @param WorkingMemory $workingMemory
     *
     * @return Rule
     */
    public function selectPreferredRule(array $rules, WorkingMemory $workingMemory);
}