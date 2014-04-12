<?php

namespace Cpliakas\DynamoDb\ODM\Renderer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class Date implements AttributeRendererInterface
{
    /**
     * {@inheritDoc}
     */
    public function render(AttributeEvent $event)
    {
        $date = new \DateTime($event->getValue());
        $event->setValue($date);
    }
}
