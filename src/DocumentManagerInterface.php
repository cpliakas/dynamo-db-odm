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
     * @param mixed $key
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface|false
     */
    public function read($entityClass, $key);

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
     * @param mixed $key
     *
     * @return bool
     */
    public function deleteByKey($entityClass, $key);

    /**
     * @param string $entityClass
     * @param mixed $key
     *
     * @return bool
     */
    public function exists($entityClass, $key);

    /**
     * Executes a query command.
     *
     * @param string $entityClass
     * @param array|\Cpliakas\DynamoDb\ODM\ConditionsInterface $commandOptions
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface[]
     *
     * @throws \InvalidArgumentException
     */
    public function query($entityClass, $commandOptions);

    /**
     * Executes a scan command.
     *
     * @param string $entityClass
     * @param array|\Cpliakas\DynamoDb\ODM\ConditionsInterface $commandOptions
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface[]
     *
     * @throws \InvalidArgumentException
     */
    public function scan($entityClass, $commandOptions);
}
