<?php

namespace Tests\CommandTests\Models\User\Commands;

final class RegisterUser
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


}