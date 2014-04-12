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
