<?php

namespace Tests\ConfigTests\Projections;

use APPelit\LaraSauce\Projection\RebuildableProjection;
use Tests\ConfigTests\User;
use EventSauce\EventSourcing\Message;

final class UserProjection implements RebuildableProjection
{
    /**
     * @return void
     */
    public function reset()
    {
        User::toBase()->truncate();
    }

    /**
     * @param Message $message
     * @return mixed
     */
    public function handle(Message $message)
    {
        $event = $message->event();
        $id = $message->aggregateRootId();
        if (!$id) {
            return;
        }

        if ($event instanceof \Tests\ConfigTests\Models\User\Events\UserRegistered) {
            User::create([
                'id' => $id->toString(),
                'username' => $event->username()->toString(),
                'email' => $event->email()->toString(),
                'password' => $event->password()->toString(),
            ]);
        } else if ($event instanceof \Tests\ConfigTests\Models\User\Events\EmailChanged) {
            $user = User::find($id->toString());
            if (!$user) {
                return;
            }

            $user->update(['email' => $event->email()->toString()]);
        } else if ($event instanceof \Tests\ConfigTests\Models\User\Events\PasswordChanged) {
            $user = User::find($id->toString());
            if (!$user) {
                return;
            }

            $user->update(['password' => $event->password()->toString()]);
        } else if ($event instanceof \Tests\ConfigTests\Models\User\Events\UsernameChanged) {
            $user = User::find($id->toString());
            if (!$user) {
                return;
            }

            $user->update(['username' => $event->username()->toString()]);
        }

        return;
    }
}
