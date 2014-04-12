<?php

namespace Cpliakas\DynamoDb\ODM\Transformer;

/**
 * Transformers convert a raw value to something else (e.g. a hashed password)
 * before storing it in the database.
 */
interface AttributeTransformerInterface
{
    /**
     * @param \Cpliakas\DynamoDb\ODM\Event\AttributeEvent $event
     */
    public function transform(Event\AttributeEvent $event);
}
