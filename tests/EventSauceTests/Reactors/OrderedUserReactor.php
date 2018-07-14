<?php

namespace Tests\EventSauceTests\Reactors;

use APPelit\LaraSauce\Reactor;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use Tests\EventSauceTests\Models\User\Events\UserRegistered;

final class OrderedUserReactor implements Consumer, Reactor
{
    public function handle(Message $message)
    {
        $event = $message->event();
        $id = $message->aggregateRootId();
        if (!$id) {
            return;
        }

        if ($event instanceof UserRegistered) {

        }
    }
}
