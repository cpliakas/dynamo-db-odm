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
     * {@inheritDoc}
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
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
