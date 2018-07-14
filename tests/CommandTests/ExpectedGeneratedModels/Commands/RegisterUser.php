<?php

namespace Tests\CommandTests\GeneratedModels\User\Commands;

final class RegisterUser
{
    /**
     * @var \Tests\CommandTests\GeneratedModels\User\Types\NameType
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \Tests\CommandTests\GeneratedModels\User\Types\UsernameType
     */
    private $username;

    /**
     * @var \Tests\CommandTests\GeneratedModels\User\Types\PasswordType
     */
    private $password;

    /**
     * @param \Tests\CommandTests\GeneratedModels\User\Types\NameType $name
     * @param string $email
     * @param \Tests\CommandTests\GeneratedModels\User\Types\UsernameType $username
     * @param \Tests\CommandTests\GeneratedModels\User\Types\PasswordType $password
     */
    public function __construct(
        \Tests\CommandTests\GeneratedModels\User\Types\NameType $name,
        string $email,
        \Tests\CommandTests\GeneratedModels\User\Types\UsernameType $username,
        \Tests\CommandTests\GeneratedModels\User\Types\PasswordType $password
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return \Tests\CommandTests\GeneratedModels\User\Types\NameType
     */
    public function name(): \Tests\CommandTests\GeneratedModels\User\Types\NameType
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
     * @return \Tests\CommandTests\GeneratedModels\User\Types\UsernameType
     */
    public function username(): \Tests\CommandTests\GeneratedModels\User\Types\UsernameType
    {
        return $this->username;
    }

    /**
     * @return \Tests\CommandTests\GeneratedModels\User\Types\PasswordType
     */
    public function password(): \Tests\CommandTests\GeneratedModels\User\Types\PasswordType
    {
        return $this->password;
    }
}