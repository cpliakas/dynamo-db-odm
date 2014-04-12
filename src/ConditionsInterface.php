<?php

namespace Cpliakas\DynamoDb\ODM;

use Aws\DynamoDb\Enum\ComparisonOperator;

interface ConditionsInterface
{
    /**
     * @param type $values
     * @param array $values
     * @param string $operator
     *
     * @return \Cpliakas\DynamoDb\ODM\ConditionsInterface
     */
    public function addCondition($attribute, $values, $operator = ComparisonOperator::EQ);

    /**
     * @return array
     */
    public function getConditions();
}
