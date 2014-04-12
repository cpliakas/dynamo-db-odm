<?php

namespace Cpliakas\DynamoDb\ODM\Event;

use Cpliakas\DynamoDb\ODM\Entity;
use Symfony\Component\EventDispatcher\Event;

class AttributeEvent extends Event
{
    /**
     * @var \Cpliakas\DynamoDb\ODM\Entity
     */
    protected $entity;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param \Cpliakas\DynamoDb\ODM\Entity $entity
     * @param string $index
     * @param mixed $value
     */
    public function __construct(Entity $entity, $index, $value)
    {
        $this->entity = $entity;
        $this->index  = $index;
        $this->value  = $value;
    }

    /**
     * @return \Cpliakas\DynamoDb\ODM\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
