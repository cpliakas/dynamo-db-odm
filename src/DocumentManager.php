<?php

namespace Cpliakas\DynamoDb\ODM;

use Aws\DynamoDb\DynamoDbClient;
use Guzzle\Service\Resource\Model;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DocumentManager implements DocumentManagerInterface
{
    /**
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    protected $dynamoDb;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var bool
     */
    protected $consistentRead;

    /**
     * @var array
     */
    protected $entityNamespaces;

    /**
     * @var string
     */
    protected $tablePrefix;

    /**
     * @var string
     */
    protected $tableSuffix;

    /**
     * @param \Aws\DynamoDb\DynamoDbClient $dynamoDb
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param array $conf
     */
    public function __construct(DynamoDbClient $dynamoDb, EventDispatcherInterface $dispatcher = null, array $conf = array())
    {
        $this->dynamoDb   = $dynamoDb;
        $this->dispatcher = $dispatcher ?: new EventDispatcher();

        $conf += array(
            'entity.consistent_read' => true,
            'entity.namespaces'      => array(),
            'table.prefix'           => '',
            'table.suffix'           => '',
        );

        $this->consistentRead   = (bool) $conf['entity.consistent_read'];
        $this->entityNamespaces = $conf['entity.namespaces'];
        $this->tablePrefix      = $conf['table.prefix'];
        $this->tableSuffix      = $conf['table.suffix'];
    }

    /**
     * {@inheritDoc}
     */
    public function create(EntityInterface $entity)
    {
        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_CREATE, $entity);
        $model = $this->save($entity);
        $this->dispatchEntityResponseEvent(Events::ENTITY_POST_CREATE, $entity, $model);
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-dynamodb.html#retrieving-items
     */
    public function read($entityClass, $primaryKey, $rangeKey = null)
    {
        $entity = $this->entityFactory($entityClass)
            ->setPrimaryKey($primaryKey)
            ->setRangeKey($rangeKey)
        ;

        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_READ, $entity);

        $model = $this->dynamoDb->getItem(array(
            'ConsistentRead' => $this->consistentRead,
            'TableName'      => $this->getEntityTable($entity),
            'Key'            => $this->formatKeyCondition($entity),
        ));

        if (isset($model['Item'])) {
            $this->populateEntity($entity, $model['Item']);
            $this->dispatchEntityRequestEvent(Events::ENTITY_POST_READ, $entity, $model);
            return $entity;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function update(EntityInterface $entity)
    {
        $this->dispatchEntityEvent(Events::ENTITY_PRE_UPDATE, $entity);
        $model = $this->save($entity);
        $this->dispatchEntitySaveEvent(Events::ENTITY_POST_UPDATE, $entity, $model);
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.DynamoDb.DynamoDbClient.html#_deleteItem
     */
    public function delete(EntityInterface $entity)
    {
        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_DELETE, $entity);

        $model = $this->dynamoDb->deleteItem(array(
            'TableName' => $this->getEntityTable($entity),
            'Key' => $this->formatKeyCondition($entity),
        ));

        $this->dispatchEntityResponseEvent( Events::ENTITY_POST_DELETE, $entity, $model);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByKey($entityClass, $primaryKey, $rangeKey = null)
    {
        $entity = $this->entityFactory($entityClass)
            ->setPrimaryKey($primaryKey)
            ->setRangeKey($rangeKey)
        ;

        return $this->delete($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function exists($entityClass, $primaryKey, $rangeKey = null)
    {
        $entity = $this->entityFactory($entityClass)
            ->setPrimaryKey($primaryKey)
            ->setRangeKey($rangeKey)
        ;

        $model = $this->dynamoDb->getItem(array(
            'ConsistentRead' => $this->consistentRead,
            'TableName'      => $this->getEntityTable($entity),
            'Key'            => $this->formatKeyCondition($entity),
        ));

        return isset($model['Item']);
    }

    /**
     * {@inheritDoc}
     */
    public function query($entityClass, Conditions $conditions, array $options = array())
    {
        $query = array(
            'TableName' => $this->getEntityTable($entityClass),
            'KeyConditions' => $this->renderConditions($conditions),
        ) + $options;

        $iterator = $this->dynamoDb->getIterator('Query', $query);

        $entities = array();
        foreach ($iterator as $item) {
            $data = array();
            foreach ($item as $attribute => $value) {
                $rawValue = current($value);
                $data[$attribute] = (key($value) != 'N') ? (string) $rawValue : (int) $rawValue;
            }
            $entities[] = $this->entityFactory($entityClass, $data);
        }

        return $entities;
    }

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     *
     * @return \Guzzle\Service\Resource\Model
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-dynamodb.html#adding-items
     */
    public function save(EntityInterface $entity)
    {
        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_SAVE, $entity);

        $model = $this->dynamoDb->putItem(array(
            'TableName' => $this->getEntityTable($entity),
            'Item' => $this->dynamoDb->formatAttributes((array) $entity),
            'ReturnConsumedCapacity' => 'TOTAL'
        ));

        $this->dispatchEntityRequestEvent(Events::ENTITY_POST_SAVE, $entity);
        return $model;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function setConsistentRead($consistentRead)
    {
        $this->consistentRead = (bool) $consistentRead;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsistentRead()
    {
        return $this->consistentRead;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function registerEntityNamesapce($namespace)
    {
        $this->entityNamespaces[] = $namespace;
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function setTableSuffix($suffix)
    {
        $this->tableSuffix = $suffix;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTableSuffix()
    {
        return $this->tableSuffix;
    }

    /**
     * Returns the entity's fully qualified class name.
     *
     * @param string $entityClass
     *
     * @throws \DomainException
     */
    public function getEntityClass($entityClass)
    {
        $found = class_exists($entityClass);

        if ($found) {
            $reflection = new \ReflectionClass($entityClass);
            $fqcn = $reflection->getName();
        } elseif (strpos('\\', $entityClass) !== 0) {
            foreach ($this->entityNamespaces as $namespace) {
                $fqcn = rtrim($namespace, '\\') . '\\' . $entityClass;
                if (class_exists($fqcn)) {
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            throw new \DomainException('Entity class not found: ' . $entityClass);
        }

        return $fqcn;
    }

    /**
     * @param string $entityClass
     * @param mixed $data
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     *
     * @throws \DomainException
     */
    public function entityFactory($entityClass, $data = array())
    {
        $fqcn = $this->getEntityClass($entityClass);
        return $fqcn::factory($this->dispatcher, $data);
    }

    /**
     * @param string $entityClass
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface|string
     *
     * @throws \DomainException
     */
    public function getEntityTable($entity)
    {
        if (!$entity instanceof EntityInterface) {
            $entity = $this->getEntityClass($entity);
        }
        return $this->tablePrefix . $entity::getTable() . $this->tableSuffix;
    }

    /**
     * {@inheritDoc}
     */
    public function renderConditions(Conditions $conditions)
    {
        $rendered = array();
        foreach ($conditions->getConditions() as $attribute => $condition) {
            $rendered[$attribute] = array(
                'AttributeValueList' => $this->dynamoDb->formatAttributes($condition['values']),
                'ComparisonOperator' => $condition['operator'],
            );
        }
        return $rendered;
    }

    /**
     * @param array $item
     *
     * @return array
     */
    public function flattenArray(array $item)
    {
        $array = array();
        foreach ($item as $property => $value) {
            $array[$property] = current($value);
        }
        return $array;
    }

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     * @param array $item
     */
    public function populateEntity(EntityInterface $entity, array $item)
    {
        if ($entity instanceof \ArrayObject) {
            $entity->exchangeArray($this->flattenArray($item));
        } else {
            $flattened = $this->flattenArray($item);
            foreach ($flattened as $attribute => $value) {
                $entity->setAttribute($attribute, $value);
            }
        }
    }

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     *
     * @return array
     */
    public function formatKeyCondition(EntityInterface $entity)
    {
        $attributes = array(
            $entity::getPrimaryKeyAttribute() => $entity->getPrimaryKey(),
        );

        $rangeKeyAttribute = $entity::getRangeKeyAttribute();
        if ($rangeKeyAttribute !== false) {
            $attributes[$rangeKeyAttribute] = $entity->getRangeKey();
        }

        return $this->dynamoDb->formatAttributes($attributes);
    }

    /**
     * @param string $eventName
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     */
    protected function dispatchEntityRequestEvent($eventName, Entity $entity)
    {
        $event = new Event\EntityRequestEvent($entity);
        $this->dispatcher->dispatch($eventName, $event);
    }

    /**
     * @param string $eventName
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     * @param \Guzzle\Service\Resource\Model $model
     */
    protected function dispatchEntityResponseEvent($eventName, Entity $entity, Model $model)
    {
        $event = new Event\EntityResponseEvent($entity, $model);
        $this->dispatcher->dispatch($eventName, $event);
    }
}
