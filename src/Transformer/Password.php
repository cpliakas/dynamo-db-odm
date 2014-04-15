<?php

namespace Cpliakas\DynamoDb\ODM\Transformer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class Password implements AttributeTransformerInterface
{
    /**
     * @var string
     */
    protected $algo;

    /**
     * @var int
     */
    protected $cost;

    /**
     * @param string $algo
     * @param int $cost
     */
    public function __construct($algo = PASSWORD_BCRYPT, $cost = 12)
    {
        $this->algo = $algo;
        $this->cost = $cost;
    }

    /**
     * {@inheritDoc}
     */
    public function transform(AttributeEvent $event)
    {
        $plainText = $event->getValue();
        $hash = password_hash($plainText, $this->algo, array('cost' => $this->cost));
        $event->setValue($hash);
    }
}
