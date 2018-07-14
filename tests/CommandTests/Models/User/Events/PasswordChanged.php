<?php

namespace Tests\CommandTests\Models\User\Events;

use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class PasswordChanged implements SerializableEvent
{
    /**
     * @var \Tests\CommandTests\Models\User\Types\Password
     */
    private $password;

    public function __construct(
        \Tests\CommandTests\Models\User\Types\Password $password
    ) {
        $this->password = $password;
    }

    public function password(): \Tests\CommandTests\Models\User\Types\Password
    {
        return $this->password;
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new PasswordChanged(
            \Tests\CommandTests\Models\User\Types\Password::fromPayload($payload['password']));
    }

    public function toPayload(): array
    {
        return [
            'password' => $this->password->toPayload(),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function withPassword(\Tests\CommandTests\Models\User\Types\Password $password): PasswordChanged
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function with(): PasswordChanged
    {
        return new PasswordChanged(
            \Tests\CommandTests\Models\User\Types\Password::fromPayload('example-password')
        );
    }

}