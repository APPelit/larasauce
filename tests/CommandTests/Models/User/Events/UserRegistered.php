<?php

namespace Tests\CommandTests\Models\User\Events;

use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class UserRegistered implements SerializableEvent
{
    /**
     * @var \Tests\CommandTests\Models\User\Types\Name
     */
    private $name;

    /**
     * @var \Tests\CommandTests\Models\User\Types\Email
     */
    private $email;

    /**
     * @var \Tests\CommandTests\Models\User\Types\Username
     */
    private $username;

    /**
     * @var \Tests\CommandTests\Models\User\Types\Password
     */
    private $password;

    public function __construct(
        \Tests\CommandTests\Models\User\Types\Name $name,
        \Tests\CommandTests\Models\User\Types\Email $email,
        \Tests\CommandTests\Models\User\Types\Username $username,
        \Tests\CommandTests\Models\User\Types\Password $password
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
    }

    public function name(): \Tests\CommandTests\Models\User\Types\Name
    {
        return $this->name;
    }

    public function email(): \Tests\CommandTests\Models\User\Types\Email
    {
        return $this->email;
    }

    public function username(): \Tests\CommandTests\Models\User\Types\Username
    {
        return $this->username;
    }

    public function password(): \Tests\CommandTests\Models\User\Types\Password
    {
        return $this->password;
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new UserRegistered(
            \Tests\CommandTests\Models\User\Types\Name::fromPayload($payload['name']),
            \Tests\CommandTests\Models\User\Types\Email::fromPayload($payload['email']),
            \Tests\CommandTests\Models\User\Types\Username::fromPayload($payload['username']),
            \Tests\CommandTests\Models\User\Types\Password::fromPayload($payload['password']));
    }

    public function toPayload(): array
    {
        return [
            'name' => $this->name->toPayload(),
            'email' => $this->email->toPayload(),
            'username' => $this->username->toPayload(),
            'password' => $this->password->toPayload(),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function withName(\Tests\CommandTests\Models\User\Types\Name $name): UserRegistered
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function withEmail(\Tests\CommandTests\Models\User\Types\Email $email): UserRegistered
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function withUsername(\Tests\CommandTests\Models\User\Types\Username $username): UserRegistered
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function withPassword(\Tests\CommandTests\Models\User\Types\Password $password): UserRegistered
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function with(): UserRegistered
    {
        return new UserRegistered(
            \Tests\CommandTests\Models\User\Types\Name::fromPayload('John Doe'),
            \Tests\CommandTests\Models\User\Types\Email::fromPayload('user@example.com'),
            \Tests\CommandTests\Models\User\Types\Username::fromPayload('example-user'),
            \Tests\CommandTests\Models\User\Types\Password::fromPayload('example-password')
        );
    }

}