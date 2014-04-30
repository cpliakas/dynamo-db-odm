<?php

namespace Cpliakas\DynamoDb\ODM\Transformer;

use Cpliakas\DynamoDb\ODM\Event\AttributeEvent;

/**
 * Generates a random Optimus Prime quote if "transform and roll out" is set
 * as the attribute's value.
 */
class OptimusPrime implements AttributeTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform(AttributeEvent $event)
    {
        $value = $event->getValue();
        if (strtolower($value) == 'transform and roll out') {
            $event->setValue($this->getQuote());
        }
    }

    /**
     * Returns a random Optimus prime quote.
     *
     * @return string
     *
     * @see http://transformersrollout.wordpress.com/2010/09/18/10-optimus-prime-quotes-useful-in-the-workplace/
     */
    public function getQuote()
    {
        $quotes = array(
            0 => 'Fate rarely calls upon us at a moment of our choosing.',
            1 => 'Freedom is the right of all sentient beings.',
            2 => 'Sometimes even the wisest of men and machines can be in error.',
            3 => 'We lost a great comrade, but gained new ones. Thank you, all of you. You honor us with your bravery.',
            4 => 'Like us, there\'s more to them than meets the eye.',
            5 => 'I will accept this burden with all that I am!',
            6 => 'There\'s a thin line between being a hero and being a memory.',
            7 => 'We\'re putting your company into bankruptcy.',
            8 => 'Until that day… till all are one.',
            9 => 'It\'s been an honor serving with you all.',
        );

        $key = mt_rand(0, 9);
        return $quotes[$key];
    }
}
