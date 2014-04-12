<?php

namespace Cpliakas\DynamoDb\ODM;

use Aws\DynamoDb\Enum\ComparisonOperator;

class Conditions extends \ArrayObject
{
    /**
     * @var array
     */
    protected $conditions = array();

    /**
     * @param type $values
     * @param array $values
     * @param string $operator
     *
     * @return \DamElastic\Resource\Database\Conditions
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
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
