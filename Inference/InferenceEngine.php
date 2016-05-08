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

use Jhg\ExpertSystem\ConflictResolution\RandomStrategy;
use Jhg\ExpertSystem\ConflictResolution\StrategyInterface;
use Jhg\ExpertSystem\Knowledge\KnowledgeBase;
use Jhg\ExpertSystem\Rule\NativePhpRuleExecutor;
use Jhg\ExpertSystem\Rule\Rule;
use Jhg\ExpertSystem\Rule\RuleExecutorInterface;
use Jhg\ExpertSystem\Rule\RuleRunDecorator;

/**
 * Class InferenceEngine
 */
class InferenceEngine
{

    /**
     * @var RuleExecutorInterface
     */
    protected $ruleExecutor;

    /**
     * @var StrategyInterface
     */
    protected $conflictResolutionStrategy;

    /**
     * @var InferenceProfiler
     */
    protected $inferenceProfiler;

    /**
     * InferenceEngine constructor.
     *
     * @param RuleExecutorInterface|null $ruleExecutor
     * @param StrategyInterface|null     $conflictResolutionStrategy
     * @param InferenceProfiler|null     $inferenceProfiler
     */
    public function __construct(RuleExecutorInterface $ruleExecutor = null, StrategyInterface $conflictResolutionStrategy = null, InferenceProfiler $inferenceProfiler = null)
    {
        $this->ruleExecutor = $ruleExecutor ? : new NativePhpRuleExecutor();
        $this->conflictResolutionStrategy = $conflictResolutionStrategy ? : new RandomStrategy();
        $this->inferenceProfiler = $inferenceProfiler;

        if ($inferenceProfiler && $this->ruleExecutor instanceof InferenceProfilerAwareInterface) {
            $this->ruleExecutor->setInferenceProfiler($inferenceProfiler);
        }

        if ($inferenceProfiler && $this->conflictResolutionStrategy instanceof InferenceProfilerAwareInterface) {
            $this->conflictResolutionStrategy->setInferenceProfiler($inferenceProfiler);
        }
    }

    /**
     * @param KnowledgeBase $knowledgeBase
     * @param WorkingMemory $workingMemory
     *
     * @return RuleRunDecorator[]
     */
    protected function getMatchedRules(KnowledgeBase $knowledgeBase, WorkingMemory $workingMemory)
    {
        $this->inferenceProfiler && $this->inferenceProfiler->startIteration();
        $this->inferenceProfiler && $this->inferenceProfiler->startMatchingRules();

        /** @var RuleRunDecorator[] $matchedRules */
        $matchedRules = [];

        /** @var Rule $rule */
        foreach ($knowledgeBase->getRules() as $rule) {
            // if there are already matched rules and they're higher priority dismatch this one
            if (sizeof($matchedRules) > 0 && $rule->getPriority() < $matchedRules[0]->getPriority()) {
                $this->inferenceProfiler && $this->inferenceProfiler->addMatchingRuleCheck($rule, 'Skip by priority');

                break;
            }

            // skip already executed rules
            if ($workingMemory->isExecuted($rule)) {
                $this->inferenceProfiler && $this->inferenceProfiler->addMatchingRuleCheck($rule, 'Already executed');
                continue;
            }

            $rule = new RuleRunDecorator($rule, $workingMemory);

            $this->inferenceProfiler && $this->inferenceProfiler->addMatchingRuleCheck($rule);

            // skip if condition is not true
            if ($this->ruleExecutor->checkCondition($rule, $workingMemory)) {
                $this->inferenceProfiler && $this->inferenceProfiler->setMatchingRuleCheckResult('Rule matches');
                $matchedRules[] = $rule;
            } else {
                $this->inferenceProfiler && $this->inferenceProfiler->setMatchingRuleCheckResult('Rule does not match');
            }
        }

        $this->inferenceProfiler && $this->inferenceProfiler->endMatchingRules();

        return $matchedRules;
    }

    /**
     * @param KnowledgeBase $knowledgeBase
     *
     * @return WorkingMemory
     */
    public function run(KnowledgeBase $knowledgeBase)
    {
        $workingMemory = new WorkingMemory();
        $workingMemory->setFacts($knowledgeBase->getFacts());

        $this->inferenceProfiler && $this->inferenceProfiler->startInference($knowledgeBase->getFacts());

        while ($matchedRules = $this->getMatchedRules($knowledgeBase, $workingMemory)) {
            /** @var RuleRunDecorator $selectedRuleDecorator */
            $selectedRuleDecorator = $this->conflictResolutionStrategy->selectPreferredRule($matchedRules, $workingMemory);
            $this->ruleExecutor->execute($selectedRuleDecorator, $workingMemory);
        }

        $this->inferenceProfiler && $this->inferenceProfiler->endInference($knowledgeBase->getFacts());
        
        $knowledgeBase->setFacts($workingMemory->getAllFacts());

        return $workingMemory;
    }
}