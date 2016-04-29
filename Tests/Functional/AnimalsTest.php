<?php

namespace Jhg\ExpertSystem\Tests\Functional;

use Jhg\ExpertSystem\Inference\ExpressionLanguageRuleExecutor;
use Jhg\ExpertSystem\Inference\InferenceEngine;
use Jhg\ExpertSystem\Knowledge\KnowledgeBase;
use Jhg\ExpertSystem\Knowledge\Rule;

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
        $this->rules['r1'] = Rule::factory('animal == "bee"', '$sound = "buzz";');
        $this->rules['r2'] = Rule::factory('animal == "bird"', '$sound = "tweet";');
        $this->rules['r3'] = Rule::factory('animal == "hen"', '$sound = "cluck";');
        $this->rules['r4'] = Rule::factory('animal == "rat"', '$sound = "squeak";');
        $this->rules['r5'] = Rule::factory('fly and size == "small"', '$animal = "bee";');
        $this->rules['r6'] = Rule::factory('fly and size == "large" and legs==2', '$animal = "bird";');
        $this->rules['r7'] = Rule::factory('legs==4', '$animal = "rat";');
        $this->rules['r8'] = Rule::factory('not fly and legs==2', '$animal = "hen";');

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

        $this->assertFact('fly', $flyValue, $facts);
        $this->assertFact('size', $sizeValue, $facts);
        $this->assertFact('legs', $legsValue, $facts);
        $this->assertFact('animal', $expectedAnimal, $facts);
        $this->assertFact('sound', $expectedSound, $facts);

        $expectedRulesObjects = [];
        foreach ($expectedRules as $expectedRuleKey) {
            $expectedRulesObjects[] = $this->rules[$expectedRuleKey];
        }

        $this->assertExplanation($expectedRulesObjects, $workingMemory);
    }
}