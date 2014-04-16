<?php

namespace Cpliakas\DynamoDb\ODM\Transformer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

class RandomString implements AttributeTransformerInterface
{
    const REGEX = '/^@random(?::(\\d+))?$/';

    /**
     * @var \Symfony\Component\Security\Core\Util\SecureRandomInterface
     */
    protected $random;

    /**
     * @var int
     */
    protected $defaultLength;

    /**
     * @param \Symfony\Component\Security\Core\Util\SecureRandomInterface $random
     * @param int $defaultLength
     */
    public function __construct(SecureRandomInterface $random, $defaultLength = 8)
    {
        $this->random        = $random;
        $this->defaultLength = $defaultLength;
    }

    /**
     * {@inheritDoc}
     */
    public function transform(AttributeEvent $event)
    {
        $value = $event->getValue();
        if (preg_match(self::REGEX, $value, $matches)) {
            $length = isset($matches[1]) ? $matches[1] : $this->defaultLength;
            $event->setValue($this->generateRandomString($length));
        }
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function generateRandomString($length)
    {
        do {
            $bytes = base64_encode($this->random->nextBytes($length));
            $string = substr(strtr($bytes, array('+' => '', '/' => '', '=' => '')), 0, $length);
        } while (strlen($string) < $length);
        return $string;
    }
}
