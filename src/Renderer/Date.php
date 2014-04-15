<?php

namespace Cpliakas\DynamoDb\ODM\Renderer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class Date implements AttributeRendererInterface
{
    /**
     * @var string
     */
    protected $timezone;

    /**
     * @param string|null $timezone
     */
    public function __construct($timezone = null)
    {
        $this->timezone = $timezone;
    }

    /**
     * {@inheritDoc}
     */
    public function render(AttributeEvent $event)
    {
        $dateString = $event->getValue();
        if (ctype_digit($dateString)) {
            $dateString = '@' . $dateString;
        }

        $time = new \DateTime($dateString);
        if (isset($this->timezone)) {
            $time->setTimezone($this->timezone);
        }

        $event->setValue($time);
    }
}
