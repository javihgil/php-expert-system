<?php
require_once 'vendor/autoload.php';

use \Jhg\ExpertSystem\Inference\InferenceEngine;
use \Jhg\ExpertSystem\Knowledge\KnowledgeBase;
use \Jhg\ExpertSystem\Knowledge\KnowledgeJsonLoader;
use \Jhg\ExpertSystem\Knowledge\Rule;

$knowledgeBase = new KnowledgeBase();
$knowledgeBase->addRule(Rule::factory('x > 1', '$y = 10;'));
$knowledgeBase->addRule(Rule::factory('y > 1', '$z = 100;'));
$knowledgeBase->addRule(Rule::factory('z > 1', '$a = 1;'));
$knowledgeBase->addFact('x', 10);

//$loader = new KnowledgeJsonLoader('test1.json');
//$loader->load($knowledgeBase);

$engine = new InferenceEngine();
$engine->run($knowledgeBase);

print_r($knowledgeBase->getFacts());
