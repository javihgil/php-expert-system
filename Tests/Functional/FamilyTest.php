<?php

namespace Jhg\ExpertSystem\Tests\Functional;

use Jhg\ExpertSystem\Inference\InferenceEngine;
use Jhg\ExpertSystem\Knowledge\KnowledgeBase;
use Jhg\ExpertSystem\Rule\Rule;

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
        $this->rules['r1'] = Rule::factory('$1->dad == $2', '$2->children[] = $1;');
        $this->rules['r2'] = Rule::factory('$1->dad == $2->dad', '$1->siblings[] = $2; $2->siblings[] = $1;');
        $this->rules['r3'] = Rule::factory('$1->dad == $2 && $2->dad', '$1->grandpas[] = $2->dad;');

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
        $joe = new \stdClass();
        $joe->name = 'Joe';
        $joe->dad = null;
        $joe->siblings = [];
        $joe->grandpas = [];
        $john = new \stdClass();
        $joe->children = [$john];
        $john->name = 'John';
        $john->dad = $joe;
        $john->children = [];
        $john->siblings = [];
        $john->grandpas = [];
        $peter = new \stdClass();
        $peter->name = 'Peter';
        $peter->dad = $john;
        $peter->children = [];
        $peter->siblings = [];
        $peter->grandpas = [];
        $mery = new \stdClass();
        $mery->name = 'Mery';
        $mery->dad = $john;
        $mery->children = [];
        $mery->siblings = [];
        $mery->grandpas = [];

        $this->knowledgeBase->addFact('joe', $joe);
        $this->knowledgeBase->addFact('john', $john);
        $this->knowledgeBase->addFact('peter', $peter);
        $this->knowledgeBase->addFact('mery', $mery);

        $workingMemory = $this->engine->run($this->knowledgeBase);

        $facts = $this->knowledgeBase->getFacts();

        $this->assertTrue(in_array($mery, $peter->siblings));
        $this->assertTrue(in_array($peter, $mery->siblings));
        $this->assertTrue(in_array($joe, $mery->grandpas));
        $this->assertTrue(in_array($joe, $peter->grandpas));
    }
}