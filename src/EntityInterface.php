<?php

namespace Cpliakas\DynamoDb\ODM;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface EntityInterface
{
    /**
     * Returns an instance of \Cpliakas\DynamoDb\ODM\EntityInterface
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param mixed $data
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public static function factory(EventDispatcherInterface $dispatcher, $data = array());

    /**
     * Returns the name of the DynamoDb table used to store the entity.
     *
     * @return string
     */
    public static function getTable();

    /**
     * Returns the attribute containing the primary key.
     *
     * @return string
     */
    public static function getPrimaryKeyAttribute();

    /**
     * Returns the attribute containing the range key, false if there is none.
     *
     * @return string|false
     */
    public static function getRangeKeyAttribute();

    /**
     * Sets the entity's primary key.
     *
     * @param mixed $primaryKey
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function setPrimaryKey($primaryKey);

    /**
     * Returns the entity's primary key.
     *
     * @return mixed
     */
    public function getPrimaryKey();

    /**
     * Sets the entity's range key.
     *
     * @param mixed $rangeKey
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function setRangeKey($rangeKey);

    /**
     * Returns the entity's range key.
     *
     * @return string|false
     */
    public function getRangeKey();

    /**
     * Adds an attribute renderer.
     *
     * Renderers convert the value stored in the database to a normalized value
     * or object that is native to PHP, e.g. a date string to \DateTime object.
     *
     * @param type $attribute
     * @param \Cpliakas\DynamoDb\ODM\Renderer\AttributeRendererInterface $renderer
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function addRenderer($attribute, Renderer\AttributeRendererInterface $renderer);

    /**
     * Adds an attribute transformer.
     *
     * Transformers convert a raw value to something else (e.g. a hashed
     * password) before storing it in the database.
     *
     * @param type $attribute
     * @param \Cpliakas\DynamoDb\ODM\Transformer\AttributeTransformerInterface $transformer
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function addTransformer($attribute, Transformer\AttributeTransformerInterface $transformer);

    /**
     * Sets an attribute's value.
     *
     * @param string $attribute
     * @param mixed $value
     * @param string|null $dataType
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function setAttribute($attribute, $value, $dataType = null);

    /**
     * Returns an attribute's value.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute);
}
