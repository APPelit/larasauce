<?php

namespace Tests\EventSauceTests\Models\User\Commands;

final class RegisterUser
{
    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Name
     */
    private $name;

    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Email
     */
    private $email;

    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Username
     */
    private $username;

    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Password
     */
    private $password;

    public function __construct(
        \Tests\EventSauceTests\Models\User\Types\Name $name,
        \Tests\EventSauceTests\Models\User\Types\Email $email,
        \Tests\EventSauceTests\Models\User\Types\Username $username,
        \Tests\EventSauceTests\Models\User\Types\Password $password
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
    }

    public function name(): \Tests\EventSauceTests\Models\User\Types\Name
    {
        return $this->name;
    }

    public function email(): \Tests\EventSauceTests\Models\User\Types\Email
    {
        return $this->email;
    }

    public function username(): \Tests\EventSauceTests\Models\User\Types\Username
    {
        return $this->username;
    }

    public function password(): \Tests\EventSauceTests\Models\User\Types\Password
    {
        return $this->password;
    }


}