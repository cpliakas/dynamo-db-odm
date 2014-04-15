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
    protected $attribute;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param \Cpliakas\DynamoDb\ODM\Entity $entity
     * @param string $attribute
     * @param mixed $value
     */
    public function __construct(Entity $entity, $attribute, $value)
    {
        $this->entity    = $entity;
        $this->attribute = $attribute;
        $this->value     = $value;
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
    public function getAttribute()
    {
        return $this->attribute;
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
