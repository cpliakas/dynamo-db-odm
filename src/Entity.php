<?php

namespace Cpliakas\DynamoDb\ODM;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Entity extends \ArrayObject implements EntityInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $classIdentifier;

    /**
     * @var array
     */
    protected $renderCache = array();

    /**
     * @var string
     */
    protected static $table;

    /**
     * @var string
     */
    protected static $hashKeyAttribute;

    /**
     * @var string
     */
    protected static $rangeKeyAttribute = false;

    /**
     * @var array
     */
    protected static $dataTypeMappings = array();

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param array $data
     */
    public function __construct(EventDispatcherInterface $dispatcher, $data = array())
    {
        $this->dispatcher = $dispatcher;
        $this->classIdentifier = str_replace('\\', '-', get_class($this));

        parent::__construct($data);
    }

    /**
     * {@inheritDoc}
     */
    public static function factory(EventDispatcherInterface $dispatcher, $data = array())
    {
        return new static($dispatcher, $data);
    }

    /**
     * {@inheritDoc}
     */
    public static function getTable()
    {
        return static::$table;
    }

    /**
     * {@inheritDoc}
     */
    public static function getHashKeyAttribute()
    {
        return static::$hashKeyAttribute;
    }

    /**
     * {@inheritDoc}
     */
    public static function getRangeKeyAttribute()
    {
        return static::$rangeKeyAttribute;
    }

    /**
     * {@inheritDoc}
     */
    public static function getDataTypeMappings()
    {
        return static::$dataTypeMappings;
    }

    /**
     * {@inheritDoc}
     */
    public function setHashKey($hash)
    {
        $this->setAttribute(static::$hashKeyAttribute, $hash);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHashKey()
    {
        return $this->getAttribute(static::$hashKeyAttribute);
    }

    /**
     * {@inheritDoc}
     */
    public function setRangeKey($range)
    {
        $this->setAttribute(static::$rangeKeyAttribute, $range);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRangeKey()
    {
        return $this->getAttribute(static::$rangeKeyAttribute);
    }

    /**
     * {@inheritDoc}
     */
    public function addRenderer($attribute, Renderer\AttributeRendererInterface $renderer)
    {
        $eventName = 'cpliakas.dynamo_db.' . $this->classIdentifier . '.' . $attribute . '.render';
        $this->dispatcher->addListener($eventName, array($renderer, 'render'));
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addTransformer($attribute, Transformer\AttributeTransformerInterface $transformer)
    {
        $eventName = 'cpliakas.dynamo_db.' . $this->classIdentifier . '.' . $attribute . '.transform';
        $this->dispatcher->addListener($eventName, array($transformer, 'transform'));
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($attribute, $value, $dataType = null)
    {
        $this->offsetSet($attribute, $value);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($attribute)
    {
        return $this->offsetGet($attribute);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cpliakas\DynamoDb\ODM\Entity
     */
    public function setAttributes(array $attributes)
    {
        $this->exchangeArray($attributes);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->getArrayCopy();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($index, $value)
    {
        $eventName = 'cpliakas.dynamo_db.' . $this->classIdentifier . '.' . $index . '.transform';
        $event = new Event\AttributeEvent($this, $index, $value);
        $this->dispatcher->dispatch($eventName, $event);

        parent::offsetSet($index, $event->getValue());
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($index)
    {
        if (!isset($this->renderCache[$index])) {
            $value = parent::offsetGet($index);

            $eventName = 'cpliakas.dynamo_db.' . $this->classIdentifier . '.' . $index . '.render';
            $event = new Event\AttributeEvent($this, $index, $value);
            $this->dispatcher->dispatch($eventName, $event);

            $this->renderCache[$index] = $event->getValue();
        }

        return $this->renderCache[$index];
    }

    /**
     * Access attributes as methods.
     *
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (isset($this[$name])) {
            if (!isset($arguments[0])) {
                return $this[$name];
            } else {
                $this[$name] = $arguments[0];
            }
        } else {
            $message = 'Call to undefined method ' . get_class($this) . '::' . $name . '()';
            throw new \BadMethodCallException($message);
        }
    }
}
