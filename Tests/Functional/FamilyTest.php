<?php

namespace Jhg\ExpertSystem\Tests\Functional;

use Jhg\ExpertSystem\Inference\InferenceEngine;
use Jhg\ExpertSystem\Knowledge\KnowledgeBase;
use Jhg\ExpertSystem\Knowledge\Rule;

/**
 * Class FamilyTest
 */
class FamilyTest extends AbstractFunctionalTestCase
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
        $this->engine = new InferenceEngine();

        $this->rules = [];
        $this->rules['r1'] = Rule::factory('$1.dad == $2', '$2.children[] = $1;');
        $this->rules['r2'] = Rule::factory('$1.dad == $2.dad', '$1.siblings[] = $2; $2.siblings[] = $1;');

        $this->knowledgeBase = new KnowledgeBase();
        foreach ($this->rules as $rule) {
            $this->knowledgeBase->addRule($rule);
        }
    }

    /**
     * Test Parent and children
     */
    public function testParentAndChildren()
    {
        $john = new \stdClass();
        $peter = new \stdClass();
        $peter->dad = $john;

        $this->knowledgeBase->addFact('john', $john);
        $this->knowledgeBase->addFact('peter', $peter);

        $workingMemory = $this->engine->run($this->knowledgeBase);

        $facts = $this->knowledgeBase->getFacts();

        echo "\n";
        foreach ($facts as $name => $value) {
            echo sprintf('%s: %s'."\n", $name, serialize($value));
        }
    }
}