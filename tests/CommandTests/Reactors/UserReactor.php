<?php

namespace Tests\CommandTests\Reactors;

use APPelit\LaraSauce\Reactor;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use Tests\CommandTests\Models\User\Events\UserRegistered;

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

        }
    }
}
