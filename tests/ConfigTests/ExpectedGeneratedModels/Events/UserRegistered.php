<?php

namespace Tests\ConfigTests\GeneratedModels\User\Events;

use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class UserRegistered implements SerializableEvent
{
    /**
     * @var \Tests\ConfigTests\GeneratedModels\User\Types\NameType
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType
     */
    private $username;

    /**
     * @var \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType
     */
    private $password;

    /**
     * @param \Tests\ConfigTests\GeneratedModels\User\Types\NameType $name
     * @param string $email
     * @param \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType $username
     * @param \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType $password
     */
    public function __construct(
        \Tests\ConfigTests\GeneratedModels\User\Types\NameType $name,
        string $email,
        \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType $username,
        \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType $password
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return \Tests\ConfigTests\GeneratedModels\User\Types\NameType
     */
    public function name(): \Tests\ConfigTests\GeneratedModels\User\Types\NameType
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType
     */
    public function username(): \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType
    {
        return $this->username;
    }

    /**
     * @return \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType
     */
    public function password(): \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType
    {
        return $this->password;
    }

    /**
     * @param array $payload
     * @return \EventSauce\EventSourcing\Serialization\SerializableEvent
     */
    public static function fromPayload(array $payload): SerializableEvent
    {
        return new UserRegistered(
            \Tests\ConfigTests\GeneratedModels\User\Types\NameType::fromPayload($payload['name']),
            (string) $payload['email'],
            \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType::fromPayload($payload['username']),
            \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType::fromPayload($payload['password']));
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        return [
            'name' => $this->name->toPayload(),
            'email' => (string) $this->email,
            'username' => $this->username->toPayload(),
            'password' => $this->password->toPayload(),
        ];
    }

    /**
     * @param \Tests\ConfigTests\GeneratedModels\User\Types\NameType $name
     * @return $this
     * @codeCoverageIgnore
     */
    public function withName(\Tests\ConfigTests\GeneratedModels\User\Types\NameType $name): UserRegistered
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $email
     * @return $this
     * @codeCoverageIgnore
     */
    public function withEmail(string $email): UserRegistered
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType $username
     * @return $this
     * @codeCoverageIgnore
     */
    public function withUsername(\Tests\ConfigTests\GeneratedModels\User\Types\UsernameType $username): UserRegistered
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType $password
     * @return $this
     * @codeCoverageIgnore
     */
    public function withPassword(\Tests\ConfigTests\GeneratedModels\User\Types\PasswordType $password): UserRegistered
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return $this
     * @codeCoverageIgnore
     */
    public static function with(): UserRegistered
    {
        return new UserRegistered(
            \Tests\ConfigTests\GeneratedModels\User\Types\NameType::fromPayload('John Doe'),
            (string) 'user@example.com',
            \Tests\ConfigTests\GeneratedModels\User\Types\UsernameType::fromPayload('example-user'),
            \Tests\ConfigTests\GeneratedModels\User\Types\PasswordType::fromPayload('example-password')
        );
    }
}