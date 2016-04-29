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

/**
 * Class Rule
 */
class Rule
{
    /**
     * @var string
     */
    protected $condition;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @param string $condition
     * @param string $action
     * @param int    $priority
     *
     * @return Rule
     */
    public static function factory($condition, $action, $priority = 0)
    {
        $rule = new self();
        $rule->setCondition($condition);
        $rule->setAction($action);
        $rule->setPriority($priority);

        return $rule;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     *
     * @return $this
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasConditionWildcards()
    {
        return (bool) preg_match('/\$[0-9]+/', $this->getCondition());
    }

    /**
     * @return array
     */
    public function getConditionWildcards()
    {
        if (preg_match('/(\$[0-9]+)/', $this->getCondition(), $matches)) {
            return array_unique($matches);
        }

        return [];
    }
}