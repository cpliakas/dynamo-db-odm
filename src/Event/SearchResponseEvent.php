<?php

namespace Cpliakas\DynamoDb\ODM\Event;

use Aws\Common\Iterator\AwsResourceIterator;
use Symfony\Component\EventDispatcher\Event;

class SearchResponseEvent extends Event
{
    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var \Aws\Common\Iterator\AwsResourceIterator
     */
    protected $iterator;

    /**
     * @param string $entityClass
     * @param \Aws\Common\Iterator\AwsResourceIterator $iterator
     */
    public function __construct($entityClass, AwsResourceIterator $iterator)
    {
        $this->entityClass = $entityClass;
        $this->iterator    = $iterator;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return \Aws\Common\Iterator\AwsResourceIterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }
}
