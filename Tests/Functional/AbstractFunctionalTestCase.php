<?php

namespace Jhg\ExpertSystem\Tests\Functional;

use Jhg\ExpertSystem\Inference\WorkingMemory;
use Jhg\ExpertSystem\Knowledge\Fact;
use Jhg\ExpertSystem\Knowledge\Rule;

/**
 * Class AbstractFunctionalTestCase
 */
abstract class AbstractFunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $expectedName
     * @param string $expectedValue
     * @param Fact[] $facts
     */
    protected function assertFact($expectedName, $expectedValue, array $facts)
    {
        foreach ($facts as $name => $value) {
            if ($name == $expectedName) {
                $this->assertEquals($expectedValue, $value);

                return;
            }
        }

        $this->assertTrue(false);
    }

    /**
     * @param Rule[]        $expectedRules
     * @param WorkingMemory $workingMemory
     */
    protected function assertExplanation($expectedRules, WorkingMemory $workingMemory)
    {
        $executed = $workingMemory->getExecutedRules();

        $this->assertEquals(sizeof($expectedRules), sizeof($executed));

        foreach ($expectedRules as $i => $expectedRule) {
            $this->assertTrue(isset($executed[$i]));
            $this->assertEquals($expectedRule, $executed[$i]);
        }
    }
}