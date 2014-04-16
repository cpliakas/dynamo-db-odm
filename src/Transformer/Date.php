<?php

namespace Cpliakas\DynamoDb\ODM\Transformer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class Date implements AttributeTransformerInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function transform(AttributeEvent $event)
    {
        $value = $event->getValue();

        if ($value instanceof \DateTime) {
            $timestamp = $value->getTimestamp();
        } elseif (is_int($value)) {
            $timestamp = $value;
        } else {
            $timestamp = strtotime($value);
            if ($timestamp === false) {
                throw new \InvalidArgumentException('Expecting value to be an instance of \DateTime, a Unix timestamp, ot a textual datetime description');
            }
        }

        $event->setValue($timestamp);
    }
}
