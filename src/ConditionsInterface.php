<?php

namespace Cpliakas\DynamoDb\ODM;

use Aws\DynamoDb\Enum\ComparisonOperator;

interface ConditionsInterface
{
    /**
     * Adds a condition to the command.
     *
     * @param type $values
     * @param array $values
     * @param string $operator
     *
     * @return \Cpliakas\DynamoDb\ODM\ConditionsInterface
     */
    public function addCondition($attribute, $values, $operator = ComparisonOperator::EQ);

    /**
     * Sets an option for the command, e.g. "Select": "COUNT".
     *
     * @param string $option
     * @param mixed $value
     *
     * @return \Cpliakas\DynamoDb\ODM\ConditionsInterface
     */
    public function setOption($option, $value);

    /**
     * @return array
     */
    public function getConditions();

    /**
     * @return array
     */
    public function getOptions();
}
