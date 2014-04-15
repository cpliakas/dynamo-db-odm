<?php

namespace Cpliakas\DynamoDb\ODM\Renderer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class Binary implements AttributeRendererInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \UnexpectedValueException
     */
    public function render(AttributeEvent $event)
    {
        $data = base64_decode($event->getValue());
        if (false === $data) {
           throw new \UnexpectedValueException('Error decoding data in the ' . $event->getAttribute() . ' attribute');
        }
        $event->setValue($data);
    }
}
