<?php

/*
 * This file is part of the PhpExpertSystem package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\KnowledgeBase;

use Jhg\ExpertSystem\Knowledge\KnowledgeBase;

/**
 * Interface KnowledgeLoaderInterface
 */
interface KnowledgeLoaderInterface
{
    /**
     * @param KnowledgeBase $knowledgeBase
     */
    public function load(KnowledgeBase $knowledgeBase);
}