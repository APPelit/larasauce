<?php

namespace Tests\CommandTests\Models\User\Events;

use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class EmailChanged implements SerializableEvent
{
    /**
     * @var \Tests\CommandTests\Models\User\Types\Email
     */
    private $email;

    public function __construct(
        \Tests\CommandTests\Models\User\Types\Email $email
    ) {
        $this->email = $email;
    }

    public function email(): \Tests\CommandTests\Models\User\Types\Email
    {
        return $this->email;
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new EmailChanged(
            \Tests\CommandTests\Models\User\Types\Email::fromPayload($payload['email']));
    }

    public function toPayload(): array
    {
        return [
            'email' => $this->email->toPayload(),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function withEmail(\Tests\CommandTests\Models\User\Types\Email $email): EmailChanged
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function with(): EmailChanged
    {
        return new EmailChanged(
            \Tests\CommandTests\Models\User\Types\Email::fromPayload('user@example.com')
        );
    }

}