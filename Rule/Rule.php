<?php

/*
 * This file is part of the PhpExpertSystem package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jhg\ExpertSystem\Rule;

/**
 * Class Rule
 */
class Rule
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

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
     * @param string $name
     * @param string $condition
     * @param string $action
     * @param int    $priority
     * @param string $description
     *
     * @return Rule
     */
    public static function factory($name, $condition, $action, $priority = 0, $description = '')
    {
        $rule = new self();
        $rule->setName($name);
        $rule->setCondition($condition);
        $rule->setAction($action);
        $rule->setPriority($priority);
        $rule->setDescription($description);

        return $rule;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
        return (bool) preg_match('/\$[0-9]+/i', $this->getCondition());
    }

    /**
     * @return array
     */
    public function getConditionWildcards()
    {
        if (preg_match_all('/\$[0-9]+/i', $this->getCondition(), $matches)) {
            return array_unique($matches[0]);
        }

        return [];
    }
}