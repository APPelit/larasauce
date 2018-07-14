<?php

namespace Tests\ConfigTests\Decorators;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class DummyDecorator implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        return $message;
    }
}