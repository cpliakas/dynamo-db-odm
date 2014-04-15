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
     * @param array $commandOptions
     *
     * @return bool
     */
    public function create(EntityInterface $entity, array $commandOptions = array());

    /**
     * @param string $entityClass
     * @param mixed $primaryKey
     * @param array $commandOptions
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface|false
     */
    public function read($entityClass, $primaryKey, array $commandOptions = array());

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     * @param array $commandOptions
     *
     * @return bool
     */
    public function update(EntityInterface $entity, array $commandOptions = array());

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     * @param array $commandOptions
     *
     * @return bool
     */
    public function delete(EntityInterface $entity, array $commandOptions = array());

    /**
     * @param string $entityClass
     * @param mixed $primaryKey
     * @param array $commandOptions
     *
     * @return bool
     */
    public function deleteByKey($entityClass, $primaryKey, array $commandOptions = array());

    /**
     * @param string $entityClass
     * @param mixed $primaryKey
     * @param array $commandOptions
     *
     * @return bool
     */
    public function exists($entityClass, $primaryKey, array $commandOptions = array());

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
