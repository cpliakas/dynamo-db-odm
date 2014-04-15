<?php

namespace Cpliakas\DynamoDb\ODM\Event;

use Symfony\Component\EventDispatcher\Event;

class SearchRequestEvent extends Event
{
    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @param string $entityClass
     */
    public function __construct($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }
}
