<?php

namespace Cpliakas\DynamoDb\ODM\Transformer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class EncryptedString implements AttributeTransformerInterface
{
    /**
     * @var \Crypt_Base
     */
    protected $cipher;

    /**
     * @param \Crypt_Base $cipher
     */
    public function __construct(\Crypt_Base $cipher)
    {
        $this->cipher = $cipher;
    }

    /**
     * {@inheritDoc}
     */
    public function transform(AttributeEvent $event)
    {
        $value = $event->getValue();
        $event->setValue($this->cipher->encrypt($value));
    }
}
