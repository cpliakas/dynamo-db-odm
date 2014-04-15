<?php

namespace Cpliakas\DynamoDb\ODM\Renderer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

class EncryptedString implements AttributeRendererInterface
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
     *
     * @throws \UnexpectedValueException
     */
    public function render(AttributeEvent $event)
    {
        $cipherText = $event->getValue();

        // The Amazon SDK base64 encodes binary strings before it sends data to
        // DynamoDB, but it does not base64 decode it after it retrieves the
        // data from DynamoDB. Therefore we have to check wheter it is encoded.
        if (preg_match('@^[a-zA-Z0-9+/]+={0,2}$@', $cipherText)) {
            $cipherText = base64_decode($cipherText);
            if (false === $cipherText) {
               throw new \UnexpectedValueException('Error decoding data in the ' . $event->getAttribute() . ' attribute');
            }
        }

        $event->setValue($this->cipher->decrypt($cipherText));
    }
}
