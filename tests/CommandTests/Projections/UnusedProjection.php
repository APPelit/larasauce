<?php

namespace Tests\CommandTests\Projections;

use APPelit\LaraSauce\Projection\RebuildableProjection;
use EventSauce\EventSourcing\Message;

final class UnusedProjection implements RebuildableProjection
{
    /**
     * @return void
     */
    public function reset()
    {
        app('db')->table('command_test_users')->truncate();
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

        if ($event instanceof \Tests\CommandTests\Models\User\Events\UserRegistered) {
            app('db')->table('command_test_users')->insert([
                'id' => $id->toString(),
                'email' => $event->email()->toString(),
                'name' => $event->name()->toString(),
                'password' => $event->password()->toString(),
                'username' => $event->username()->toString(),
            ]);
        } else if ($event instanceof \Tests\CommandTests\Models\User\Events\EmailChanged) {
            app('db')->table('command_test_users')->where('id', $id->toString())->update(['email' => $event->email()->toString()]);
        } else if ($event instanceof \Tests\CommandTests\Models\User\Events\NameChanged) {
            app('db')->table('command_test_users')->where('id', $id->toString())->update(['name' => $event->name()->toString()]);
        } else if ($event instanceof \Tests\CommandTests\Models\User\Events\PasswordChanged) {
            app('db')->table('command_test_users')->where('id', $id->toString())->update(['password' => $event->password()->toString()]);
        } else if ($event instanceof \Tests\CommandTests\Models\User\Events\UsernameChanged) {
            app('db')->table('command_test_users')->where('id', $id->toString())->update(['username' => $event->username()->toString()]);
        }

        return;
    }
}
