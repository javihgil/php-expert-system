<?php

namespace Jhg\ExpertSystem\Tests\Functional;

use Jhg\ExpertSystem\Inference\InferenceEngine;
use Jhg\ExpertSystem\Knowledge\KnowledgeBase;
use Jhg\ExpertSystem\Rule\ExpressionLanguageRuleExecutor;
use Jhg\ExpertSystem\Rule\Rule;

/**
 * Class AnimalsTest
 */
class AnimalsTest extends AbstractFunctionalTestCase
{
    /**
     * @var InferenceEngine
     */
    protected $engine;

    /**
     * @var Rule[]
     */
    protected $rules;

    /**
     * @var KnowledgeBase
     */
    protected $knowledgeBase;

    /**
     * Prepares tests
     */
    public function setup()
    {
        $this->engine = new InferenceEngine(new ExpressionLanguageRuleExecutor());

        $this->rules = [];
        $this->rules['r1'] = Rule::factory('r1', 'animal == "bee"', '$sound = "buzz";');
        $this->rules['r2'] = Rule::factory('r2', 'animal == "bird"', '$sound = "tweet";');
        $this->rules['r3'] = Rule::factory('r3', 'animal == "hen"', '$sound = "cluck";');
        $this->rules['r4'] = Rule::factory('r4', 'animal == "rat"', '$sound = "squeak";');
        $this->rules['r5'] = Rule::factory('r5', 'fly and size == "small"', '$animal = "bee";');
        $this->rules['r6'] = Rule::factory('r6', 'fly and size == "large" and legs==2', '$animal = "bird";');
        $this->rules['r7'] = Rule::factory('r7', 'legs==4', '$animal = "rat";');
        $this->rules['r8'] = Rule::factory('r8', 'not fly and legs==2', '$animal = "hen";');

        $this->knowledgeBase = new KnowledgeBase();
        foreach ($this->rules as $rule) {
            $this->knowledgeBase->addRule($rule);
        }
    }

    /**
     * @return array
     */
    public function soundsDataProvider()
    {
        return [
            [true, 'small', 2, 'bee', 'buzz', ['r5','r1']],
            [false, 'small', 2, 'hen', 'cluck', ['r8','r3']],
            [true, 'large', 2, 'bird', 'tweet', ['r6','r2']],
            [false, 'large', 2, 'hen', 'cluck', ['r8','r3']],
            // [true, 'small', 4, 'bee', 'buzz', ['r7']], // wrong
            [false, 'small', 4, 'rat', 'squeak', ['r7','r4']],
            [true, 'large', 4, 'rat', 'squeak', ['r7','r4']],
            [false, 'large', 4, 'rat', 'squeak', ['r7','r4']],
        ];
    }

    /**
     * @param bool   $flyValue
     * @param string $sizeValue
     * @param int    $legsValue
     * @param string $expectedAnimal
     * @param string $expectedSound
     * @param array  $expectedRules
     *
     * @dataProvider soundsDataProvider
     */
    public function testSounds($flyValue, $sizeValue, $legsValue, $expectedAnimal, $expectedSound, array $expectedRules)
    {
        $this->knowledgeBase->addFact('fly', $flyValue);
        $this->knowledgeBase->addFact('size', $sizeValue);
        $this->knowledgeBase->addFact('legs', $legsValue);

        $workingMemory = $this->engine->run($this->knowledgeBase);

        $facts = $this->knowledgeBase->getFacts();

        $this->assertSimpleFact('fly', $flyValue, $facts);
        $this->assertSimpleFact('size', $sizeValue, $facts);
        $this->assertSimpleFact('legs', $legsValue, $facts);
        $this->assertSimpleFact('animal', $expectedAnimal, $facts);
        $this->assertSimpleFact('sound', $expectedSound, $facts);

        $expectedRulesObjects = [];
        foreach ($expectedRules as $expectedRuleKey) {
            $expectedRulesObjects[] = $this->rules[$expectedRuleKey];
        }

        $this->assertExplanation($expectedRulesObjects, $workingMemory);
    }
}