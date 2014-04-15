<?php

namespace Cpliakas\DynamoDb\ODM\Renderer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

/**
 * Renderers convert the value stored in the database to a normalized value or
 * object that is native to PHP, e.g. a date string to a \DateTime object.
 */
interface AttributeRendererInterface
{
    /**
     * @param \Cpliakas\DynamoDb\ODM\Event\AttributeEvent $event
     */
    public function render(AttributeEvent $event);
}
