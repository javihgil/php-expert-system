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
use Jhg\ExpertSystem\Knowledge\Rule;

/**
 * Class InferenceEngine
 */
class InferenceEngine
{

    /**
     * @var RuleExecutor
     */
    protected $ruleExecutor;

    /**
     * @var StrategyInterface
     */
    protected $conflictResolutionStrategy;

    /**
     * @param RuleExecutor      $ruleExecutor
     * @param StrategyInterface $conflictResolutionStrategy
     */
    public function __construct(RuleExecutor $ruleExecutor = null, StrategyInterface $conflictResolutionStrategy = null)
    {
        $this->ruleExecutor = $ruleExecutor ? : new RuleExecutor();
        $this->conflictResolutionStrategy = $conflictResolutionStrategy ? : new RandomStrategy();
    }

    /**
     * @param KnowledgeBase $knowledgeBase
     * @param WorkingMemory $workingMemory
     *
     * @return Rule[]
     */
    protected function getMatchedRules(KnowledgeBase $knowledgeBase, WorkingMemory $workingMemory)
    {
        /** @var Rule[] $matchedRules */
        $matchedRules = [];

        /** @var Rule $rule */
        foreach ($knowledgeBase->getRules() as $rule) {
            // if there are already matched rules and they're higher priority dismatch this one
            if (sizeof($matchedRules) > 0 && $rule->getPriority() < $matchedRules[0]->getPriority()) {
                break;
            }

            // skip already executed rules and not condition verifying ones
            if (!$workingMemory->isExecuted($rule) && $this->ruleExecutor->checkCondition($rule, $workingMemory->getAllFacts())) {
                $matchedRules[] = $rule;
            }
        }

        return $matchedRules;
    }

    /**
     * @param KnowledgeBase $knowledgeBase
     */
    public function run(KnowledgeBase $knowledgeBase)
    {
        $workingMemory = new WorkingMemory();
        $workingMemory->setFromFacts($knowledgeBase->getFacts());

        while (true) {
            // Get the rules that has matching conditions
            $matchedRules = $this->getMatchedRules($knowledgeBase, $workingMemory);

            if (empty($matchedRules)) {
                // No more rules?
                break;
            } else {
                /** @var Rule $selectedRule */
                if (sizeof($matchedRules) == 1) {
                    // just one rule
                    $selectedRule = $matchedRules[0];
                } else {
                    // there are multiple rules with same priority
                    $selectedRule = $this->conflictResolutionStrategy->selectPreferredRule($matchedRules, $workingMemory);
                }

                $newFacts = $this->ruleExecutor->execute($selectedRule, $workingMemory->getAllFacts());
                $workingMemory->setAllFacts($newFacts);
                $workingMemory->setExecuted($selectedRule);
            }
        }

        $knowledgeBase->setFactsArray($workingMemory->getAllFacts());
    }
}