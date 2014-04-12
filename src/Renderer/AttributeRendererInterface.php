<?php

namespace Cpliakas\DynamoDb\ODM\Renderer;

/**
 * Renderers convert the value stored in the database to a normalized value or
 * object that is native to PHP, e.g. a date string to a \DateTime object.
 */
interface AttributeRendererInterface
{
    /**
     * @param \Cpliakas\DynamoDb\ODM\Event\AttributeEvent $event
     */
    public function render(Event\AttributeEvent $event);
}
