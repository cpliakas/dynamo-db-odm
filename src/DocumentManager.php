<?php

namespace Cpliakas\DynamoDb\ODM;

use Aws\DynamoDb\DynamoDbClient;
use Guzzle\Service\Resource\Model;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DocumentManager implements DocumentManagerInterface
{
    const CONSUMED_CAPACITY_NONE    = 'NONE';
    const CONSUMED_CAPACITY_TOTAL   = 'TOTAL';
    const CONSUMED_CAPACITY_INDEXES = 'INDEXES';

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
     * @var string
     */
    protected $returnConsumedCapacity;

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
            'command.consistent_read'          => false,
            'command.return_consumed_capacity' => self::CONSUMED_CAPACITY_NONE,
            'entity.namespaces'                => array(),
            'table.prefix'                     => '',
            'table.suffix'                     => '',
        );

        $this->consistentRead         = (bool) $conf['command.consistent_read'];
        $this->returnConsumedCapacity = $conf['command.return_consumed_capacity'];
        $this->entityNamespaces       = $conf['entity.namespaces'];
        $this->tablePrefix            = $conf['table.prefix'];
        $this->tableSuffix            = $conf['table.suffix'];
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
     * {@inheritDoc}
     *
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
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
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-dynamodb.html#retrieving-items
     */
    public function read($entityClass, $primaryKey)
    {
        $entity = $this->initEntity($entityClass, $primaryKey);
        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_READ, $entity);

        $model = $this->dynamoDb->getItem(array(
            'ConsistentRead'         => $this->consistentRead,
            'TableName'              => $this->getEntityTable($entity),
            'Key'                    => $this->renderKeyCondition($entity),
            'ReturnConsumedCapacity' => $this->returnConsumedCapacity,
        ));

        if (isset($model['Item'])) {
            $this->populateEntity($entity, $model['Item']);
            $this->dispatchEntityResponseEvent(Events::ENTITY_POST_READ, $entity, $model);
            return $entity;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
     */
    public function update(EntityInterface $entity)
    {
        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_UPDATE, $entity);
        $model = $this->save($entity);
        $this->dispatchEntityResponseEvent(Events::ENTITY_POST_UPDATE, $entity, $model);
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.DynamoDb.DynamoDbClient.html#_deleteItem
     */
    public function delete(EntityInterface $entity)
    {
        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_DELETE, $entity);

        $model = $this->dynamoDb->deleteItem(array(
            'TableName' => $this->getEntityTable($entity),
            'Key'       => $this->renderKeyCondition($entity),
        ));

        $this->dispatchEntityResponseEvent( Events::ENTITY_POST_DELETE, $entity, $model);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByKey($entityClass, $primaryKey)
    {
        $entity = $this->initEntity($entityClass, $primaryKey);
        return $this->delete($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function exists($entityClass, $primaryKey)
    {
        $entity = $this->initEntity($entityClass, $primaryKey);

        $model = $this->dynamoDb->getItem(array(
            'ConsistentRead'         => $this->consistentRead,
            'TableName'              => $this->getEntityTable($entity),
            'Key'                    => $this->renderKeyCondition($entity),
            'ReturnConsumedCapacity' => $this->returnConsumedCapacity,
        ));

        return isset($model['Item']);
    }

    /**
     * {@inheritDoc}
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-dynamodb.html#query
     *
     * @throws \InvalidArgumentException
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
     */
    public function query($entityClass, $commandOptions)
    {
        return $this->executeCommand($entityClass, $commandOptions, 'Query', 'KeyConditions');
    }

    /**
     * {@inheritDoc}
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-dynamodb.html#scan
     *
     * @throws \InvalidArgumentException
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
     */
    public function scan($entityClass, $commandOptions)
    {
        return $this->executeCommand($entityClass, $commandOptions, 'Scan', 'ScanFilter');
    }

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     *
     * @return \Guzzle\Service\Resource\Model
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-dynamodb.html#adding-items
     *
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
     */
    public function save(EntityInterface $entity)
    {
        $this->dispatchEntityRequestEvent(Events::ENTITY_PRE_SAVE, $entity);

        $model = $this->dynamoDb->putItem(array(
            'TableName'              => $this->getEntityTable($entity),
            'Item'                   => $this->dynamoDb->formatAttributes((array) $entity),
            'ReturnConsumedCapacity' => $this->returnConsumedCapacity,
        ));

        $this->dispatchEntityRequestEvent(Events::ENTITY_POST_SAVE, $entity);
        return $model;
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param bool $consistentRead
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function consistentRead($consistentRead = true)
    {
        $this->consistentRead = (bool) $consistentRead;
        return $this;
    }

    /**
     * @return bool
     */
    public function getConsistentRead()
    {
        return $this->consistentRead;
    }

    /**
     * @param string $type
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function returnConsumedCapacity($type)
    {
        $this->returnConsumedCapacity = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnConsumedCapacity()
    {
        return $this->returnConsumedCapacity;
    }

    /**
     * @param string $namespace
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function registerEntityNamesapce($namespace)
    {
        $this->entityNamespaces[] = $namespace;
        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * @param string $suffix
     *
     * @return \Cpliakas\DynamoDb\ODM\DocumentManager
     */
    public function setTableSuffix($suffix)
    {
        $this->tableSuffix = $suffix;
        return $this;
    }

    /**
     * @return string
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
    protected function getEntityClass($entityClass)
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
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface|string
     *
     * @throws \DomainException
     */
    protected function getEntityTable($entity)
    {
        if (!$entity instanceof EntityInterface) {
            $entity = $this->getEntityClass($entity);
        }
        return $this->tablePrefix . $entity::getTable() . $this->tableSuffix;
    }

    /**
     * Initializes an entity with a primary and range key.
     *
     * @param string $entityClass
     * @param mixed $primaryKey
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     *
     * @throw new \InvalidArgumentException
     */
    protected function initEntity($entityClass, $primaryKey)
    {
        $primaryKey = (array) $primaryKey;

        if (!isset($primaryKey[0])) {
            throw new \InvalidArgumentException('Primary key\'s hash attribute is required');
        }

        $entity = $this->entityFactory($entityClass)->setHash($primaryKey[0]);
        if (isset($primaryKey[1])) {
            $entity->setRange($primaryKey[1]);
        }

        return $entity;
    }

    /**
     * @param \Cpliakas\DynamoDb\ODM\EntityInterface $entity
     *
     * @return array
     */
    protected function renderKeyCondition(EntityInterface $entity)
    {
        $attributes = array(
            $entity::getHashAttribute() => $entity->getHash(),
        );

        $rangeKeyAttribute = $entity::getRangeAttribute();
        if ($rangeKeyAttribute !== false) {
            $attributes[$rangeKeyAttribute] = $entity->getRange();
        }

        return $this->dynamoDb->formatAttributes($attributes);
    }

    /**
     * Renders the key conditions.
     *
     * @param \Cpliakas\DynamoDb\ODM\CommandConditionsInterface $conditions
     *
     * @return array
     */
    protected function renderConditions(ConditionsInterface $conditions)
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
     * Executes a scan or query command.
     *
     * @param string $entityClass
     * @param array|\Cpliakas\DynamoDb\ODM\KeyConditionsInterface $options
     * @param string $command
     * @param string $optionKey
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface[]
     *
     * @throws \InvalidArgumentException
     * @throws \Aws\DynamoDb\Exception\DynamoDBException
     */
    protected function executeCommand($entityClass, $commandOptions, $command, $optionKey)
    {
        if ($commandOptions instanceof ConditionsInterface) {
            $commandOptions = array($optionKey => $this->renderConditions($commandOptions)) + $commandOptions->getOptions();
        } elseif (!is_array($commandOptions)) {
            throw new \InvalidArgumentException('Expecting command options to be an array or instance of \Cpliakas\DynamoDb\ODM\KeyConditionsInterface');
        }

        $commandOptions['TableName'] = $this->getEntityTable($entityClass);
        $commandOptions += array(
            'ConsistentRead'         => $this->consistentRead,
            'ReturnConsumedCapacity' => $this->returnConsumedCapacity,
        );

        $iterator = $this->dynamoDb->getIterator($command, $commandOptions);

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
     * @param array $item
     *
     * @return array
     */
    protected function flattenArray(array $item)
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
    protected function populateEntity(EntityInterface $entity, array $item)
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
