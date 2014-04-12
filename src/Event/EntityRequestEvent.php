<?php

namespace Cpliakas\DynamoDb\ODM\Event;

use Cpliakas\DynamoDb\ODM\EntityInterface;
use Symfony\Component\EventDispatcher\Event;

class EntityRequestEvent extends Event
{
    /**
     * @var \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    protected $entity;

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     */
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
