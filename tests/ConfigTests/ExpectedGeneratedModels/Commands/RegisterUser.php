<?php

namespace Tests\ConfigTests\GeneratedModels\User\Commands;

final class RegisterUser
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
}