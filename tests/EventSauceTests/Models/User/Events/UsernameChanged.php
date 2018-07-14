<?php

namespace Tests\EventSauceTests\Models\User\Events;

use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class UsernameChanged implements SerializableEvent
{
    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Username
     */
    private $username;

    public function __construct(
        \Tests\EventSauceTests\Models\User\Types\Username $username
    ) {
        $this->username = $username;
    }

    public function username(): \Tests\EventSauceTests\Models\User\Types\Username
    {
        return $this->username;
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new UsernameChanged(
            \Tests\EventSauceTests\Models\User\Types\Username::fromPayload($payload['username']));
    }

    public function toPayload(): array
    {
        return [
            'username' => $this->username->toPayload(),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function withUsername(\Tests\EventSauceTests\Models\User\Types\Username $username): UsernameChanged
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function with(): UsernameChanged
    {
        return new UsernameChanged(
            \Tests\EventSauceTests\Models\User\Types\Username::fromPayload('example-user')
        );
    }

}