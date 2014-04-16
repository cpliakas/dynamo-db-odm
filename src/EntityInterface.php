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
     *
     * @deprecated since 0.3.1
     */
    public static function factory(EventDispatcherInterface $dispatcher, $data = array());

    /**
     * Returns the name of the DynamoDb table used to store the entity.
     *
     * @return string
     */
    public static function getTable();

    /**
     * Returns the attribute containing the primary key's hash.
     *
     * @return string
     */
    public static function getHashKeyAttribute();

    /**
     * Returns the attribute containing the primary key's range key, false if
     * there is none.
     *
     * @return string|false
     */
    public static function getRangeKeyAttribute();

    /**
     * Returns a mapping of attribute names to their corresponding data types.
     *
     * @return array
     *
     * @see Aws\DynamoDb\Enum\Type
     */
    public static function getDataTypeMappings();

    /**
     * Whether to enforce entity integrity.
     *
     * @return bool
     */
    public static function enforceEntityIntegrity();

    /**
     * Sets the value of the primary key's hash attribute.
     *
     * @param mixed $hash
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function setHashKey($hash);

    /**
     * Returns the value of the primary key's hash attribute.
     *
     * @return string|false
     */
    public function getHashKey();

    /**
     * Sets the value of the primary key's range attribute.
     *
     * @param mixed $range
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function setRangeKey($rangeKey);

    /**
     * Returns the value of the primary key's range attribute.
     *
     * @return string|false
     */
    public function getRangeKey();

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

    /**
     * Resets all attributes.
     *
     * @param array $attributes
     *
     * @return \Cpliakas\DynamoDb\ODM\EntityInterface
     */
    public function setAttributes(array $attributes);

    /**
     * Returns an associative array of all attributes.
     *
     * @return array
     */
    public function getAttributes();

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
}
