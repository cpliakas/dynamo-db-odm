<?php

namespace Cpliakas\DynamoDb\ODM\Event;

use Cpliakas\DynamoDb\ODM\EntityInterface;
use Guzzle\Service\Resource\Model;

class EntityResponseEvent extends EntityRequestEvent
{
    /**
     * @var \Guzzle\Service\Resource\Model
     */
    protected $model;

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     * @param \Guzzle\Service\Resource\Model $model
     */
    public function __construct(EntityInterface $entity, Model $model)
    {
        parent::__construct($entity);
        $this->model = $model;
    }

    /**
     * @return \Guzzle\Service\Resource\Model
     */
    public function getModel()
    {
        return $this->model;
    }
}
