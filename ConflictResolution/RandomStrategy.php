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

use Jhg\ExpertSystem\Inference\InferenceProfiler;
use Jhg\ExpertSystem\Inference\InferenceProfilerAwareInterface;
use Jhg\ExpertSystem\Inference\WorkingMemory;
use Jhg\ExpertSystem\Rule\Rule;

/**
 * Class RandomStrategy
 */
class RandomStrategy implements StrategyInterface, InferenceProfilerAwareInterface
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
     * @param Rule[]        $rules
     * @param WorkingMemory $workingMemory
     *
     * @return Rule
     */
    public function selectPreferredRule(array $rules, WorkingMemory $workingMemory)
    {
        $random = rand(0, sizeof($rules) - 1); // Calculate a random number

        $selectedRule = $rules[$random];
        
        $this->inferenceProfiler && $this->inferenceProfiler->setIterationSelectedRule($selectedRule, 'Rule selected randomly');
        
        return $selectedRule;
    }
}