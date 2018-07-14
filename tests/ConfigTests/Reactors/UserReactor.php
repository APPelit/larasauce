<?php

namespace Tests\ConfigTests\Reactors;

use APPelit\LaraSauce\Reactor;
use Tests\ConfigTests\Models\User\Events\UserRegistered;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;

final class UserReactor implements Consumer, Reactor
{
    public function handle(Message $message)
    {
        $event = $message->event();
        $id = $message->aggregateRootId();
        if (!$id) {
            return;
        }

        if ($event instanceof UserRegistered) {
            // TODO do something
        }
    }
}