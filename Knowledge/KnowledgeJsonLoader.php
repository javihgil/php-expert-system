<?php

/*
 * This file is part of the PhpExpertSystem package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\Knowledge;

use Jhg\ExpertSystem\Knowledge\Exception\KnowledgeLoaderError;
use Jhg\ExpertSystem\KnowledgeBase\KnowledgeLoaderInterface;

/**
 * Class KnowledgeJsonLoader
 *
 * @package Jhg\ExpertSystem\Knowledge
 */
class KnowledgeJsonLoader implements KnowledgeLoaderInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $jsonPath
     *
     * @throws KnowledgeLoaderError
     */
    public function __construct($jsonPath)
    {
        if (!file_exists($jsonPath)) {
            throw new KnowledgeLoaderError(sprintf('Json file does not exist at "%s".', $jsonPath));
        }

        $jsonString = file_get_contents($jsonPath);

        $data = json_decode($jsonString, true);

        if (!$data) {
            throw new KnowledgeLoaderError(sprintf('Can not decode json.', $jsonPath));
        }

        $this->data = $data;
    }


    /**
     * @param KnowledgeBase $knowledgeBase
     */
    public function load(KnowledgeBase $knowledgeBase)
    {
        foreach ($this->data['facts'] as $name => $value) {
            $knowledgeBase->add(Fact::factory($name, $value));
        }

        foreach ($this->data['rules'] as $condition => $data) {
            if (is_string($data)) {
                $action = $data;
                $priority = 0;
            } else {
                $action = $data['action'];
                $priority = isset($data['priority']) ? $data['priority'] : 0;
            }

            $knowledgeBase->add(Rule::factory($condition, $condition, $action, $priority));
        }
    }
}
