<?php

namespace Cpliakas\DynamoDb\ODM;

use Aws\DynamoDb\Enum\ComparisonOperator;

class Conditions implements ConditionsInterface
{
    /**
     * @var array
     */
    protected $conditions = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @return \Cpliakas\DynamoDb\ODM\Conditions
     */
    public static function factory()
    {
        return new static();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\Conditions
     */
    public function addCondition($attribute, $values, $operator = ComparisonOperator::EQ)
    {
        $this->conditions[$attribute] = array(
            'values' => (array) $values,
            'operator' => $operator,
        );

        return $this;
    }

    /**
     * Add null condition on a attribute
     *
     * @return \Cpliakas\DynamoDb\ODM\Conditions
     */
    public function addNullCondition($attribute)
    {
        $this->conditions[$attribute] = array(
            'operator' => ComparisonOperator::NULL,
        );

        return $this;
    }

    /**
     * Add not null condition on a attribute
     *
     * @return \Cpliakas\DynamoDb\ODM\Conditions
     */
    public function addNotNullCondition($attribute)
    {
        $this->conditions[$attribute] = array(
            'operator' => ComparisonOperator::NOT_NULL,
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\Conditions
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
