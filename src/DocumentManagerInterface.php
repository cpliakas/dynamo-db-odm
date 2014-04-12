<?php

namespace Cpliakas\DynamoDb\ODM;

interface DocumentManagerInterface
{
    /**
     * Instantiates an entity class.
     *
     * @param string $entityClass
     * @param mixed $data
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     *
     * @throws \DomainException
     */
    public function entityFactory($entityClass, $data = array());

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
     * @param \Cpliakas\DynamoDb\ODM\ConditionsInterface $conditions
     * @param array $options
     *
     * @return
     */
    public function query($entityClass, ConditionsInterface $conditions, array $options = array());
}
