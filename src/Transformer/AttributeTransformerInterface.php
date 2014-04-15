<?php

namespace Cpliakas\DynamoDb\ODM\Transformer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

/**
 * Transformers convert a raw value to something else (e.g. a hashed password)
 * before storing it in the database.
 */
interface AttributeTransformerInterface
{
    /**
     * @param \Cpliakas\DynamoDb\ODM\Event\AttributeEvent $event
     */
    public function transform(AttributeEvent $event);
}
