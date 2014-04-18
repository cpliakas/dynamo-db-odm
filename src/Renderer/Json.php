<?php

namespace Cpliakas\DynamoDb\ODM\Renderer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class Json implements AttributeRendererInterface
{
    /**
     * {@inheritDoc}
     */
    public function render(AttributeEvent $event)
    {
        $data = json_decode($event->getValue(), true);
        $event->setValue($data);
    }
}
