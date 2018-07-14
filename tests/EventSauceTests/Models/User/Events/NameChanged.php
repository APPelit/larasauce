<?php

namespace Tests\EventSauceTests\Models\User\Events;

use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class NameChanged implements SerializableEvent
{
    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Name
     */
    private $name;

    public function __construct(
        \Tests\EventSauceTests\Models\User\Types\Name $name
    ) {
        $this->name = $name;
    }

    public function name(): \Tests\EventSauceTests\Models\User\Types\Name
    {
        return $this->name;
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new NameChanged(
            \Tests\EventSauceTests\Models\User\Types\Name::fromPayload($payload['name']));
    }

    public function toPayload(): array
    {
        return [
            'name' => $this->name->toPayload(),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function withName(\Tests\EventSauceTests\Models\User\Types\Name $name): NameChanged
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function with(): NameChanged
    {
        return new NameChanged(
            \Tests\EventSauceTests\Models\User\Types\Name::fromPayload('John Doe')
        );
    }

}