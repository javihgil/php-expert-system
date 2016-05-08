<?php

/*
 * This file is part of the life-project package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\Inference;

/**
 * Interface InferenceProfilerAwareInterface
 *
 * @package Jhg\ExpertSystem\Inference
 */
interface InferenceProfilerAwareInterface
{
    /**
     * @param InferenceProfiler $inferenceProfiler
     */
    public function setInferenceProfiler(InferenceProfiler $inferenceProfiler);
}
