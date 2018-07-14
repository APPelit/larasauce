<?php

namespace Tests\ConfigTests\Decorators;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class DummyUserDecorator implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        return $message;
    }
}