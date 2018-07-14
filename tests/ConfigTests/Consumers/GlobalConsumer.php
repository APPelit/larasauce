<?php

namespace Tests\ConfigTests\Consumers;

use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;

final class GlobalConsumer implements Consumer
{
    public function handle(Message $message)
    {

    }
}
