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
     * @param RuleExecutorInterface $ruleExecutor
     * @param StrategyInterface     $conflictResolutionStrategy
     */
    public function __construct(RuleExecutorInterface $ruleExecutor = null, StrategyInterface $conflictResolutionStrategy = null)
    {
        $this->ruleExecutor = $ruleExecutor ? : new NativePhpRuleExecutor();
        $this->conflictResolutionStrategy = $conflictResolutionStrategy ? : new RandomStrategy();
    }

    /**
     * @param KnowledgeBase $knowledgeBase
     * @param WorkingMemory $workingMemory
     *
     * @return RuleRunDecorator[]
     */
    protected function getMatchedRules(KnowledgeBase $knowledgeBase, WorkingMemory $workingMemory)
    {
        /** @var RuleRunDecorator[] $matchedRules */
        $matchedRules = [];

        /** @var Rule $rule */
        foreach ($knowledgeBase->getRules() as $rule) {
            // if there are already matched rules and they're higher priority dismatch this one
            if (sizeof($matchedRules) > 0 && $rule->getPriority() < $matchedRules[0]->getPriority()) {
                break;
            }

            // skip already executed rules
            if ($workingMemory->isExecuted($rule)) {
                continue;
            }

            $rule = new RuleRunDecorator($rule, $workingMemory);

            // skip if condition is not true
            if ($this->ruleExecutor->checkCondition($rule, $workingMemory)) {
                $matchedRules[] = $rule;
            }
        }

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

        while ($matchedRules = $this->getMatchedRules($knowledgeBase, $workingMemory)) {
            /** @var RuleRunDecorator $selectedRuleDecorator */
            $selectedRuleDecorator = $this->conflictResolutionStrategy->selectPreferredRule($matchedRules, $workingMemory);
            $workingMemory->setAllFacts($this->ruleExecutor->execute($selectedRuleDecorator, $workingMemory));
            $workingMemory->setExecuted($selectedRuleDecorator->getRule());
        }

        $knowledgeBase->setFacts($workingMemory->getAllFacts());

        return $workingMemory;
    }
}