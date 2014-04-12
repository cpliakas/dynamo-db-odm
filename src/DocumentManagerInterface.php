<?php

namespace Cpliakas\DynamoDb\ODM;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface DocumentManagerInterface
{
    /**
     * Writes a new entry to DynamoDB containing the entity.
     *
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     *
     * @return bool
     */
    public function create(EntityInterface $entity);

    /**
     * @param string $entityClass
     * @param string $primaryKey
     * @param string|null $rangeKey
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface|false
     */
    public function read($entityClass, $primaryKey, $rangeKey = null);

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     *
     * @return bool
     */
    public function update(EntityInterface $entity);

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     *
     * @return bool
     */
    public function delete(EntityInterface $entity);

    /**
     * @param string $entityClass
     * @param string $primaryKey
     * @param string|null $rangeKey
     *
     * @return bool
     */
    public function deleteByKey($entityClass, $primaryKey, $rangeKey = null);

    /**
     * @param string $entityClass
     * @param string $primaryKey
     * @param string|null $rangeKey
     *
     * @return bool
     */
    public function exists($entityClass, $primaryKey, $rangeKey = null);

    /**
     * @param string $entityClass
     * @param \Cpliakas\DynamoDb\ODM\Conditions $conditions
     * @param array $options
     *
     * @return
     */
    public function query($entityClass, Conditions $conditions, array $options = array());

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManagerInterface
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher);

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher();

    /**
     * @param bool $consistentRead
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManagerInterface
     */
    public function setConsistentRead($consistentRead);

    /**
     * @return bool
     */
    public function getConsistentRead();

    /**
     * @param string $namespace
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManagerInterface
     */
    public function registerEntityNamesapce($namespace);

    /**
     * @param string $prefix
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManagerInterface
     */
    public function setTablePrefix($prefix);

    /**
     * @return string
     */
    public function getTablePrefix();

    /**
     * @param string $suffix
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManagerInterface
     */
    public function setTableSuffix($suffix);

    /**
     * @return string
     */
    public function getTableSuffix();
}
