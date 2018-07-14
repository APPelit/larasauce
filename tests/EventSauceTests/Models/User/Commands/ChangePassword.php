<?php

namespace Tests\EventSauceTests\Models\User\Commands;

final class ChangePassword
{
    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Password
     */
    private $password;

    public function __construct(
        \Tests\EventSauceTests\Models\User\Types\UserIdType $userId,
        \Tests\EventSauceTests\Models\User\Types\Password $password
    ) {
        $this->userId = $userId;
        $this->password = $password;
    }

    public function userId(): \Tests\EventSauceTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function password(): \Tests\EventSauceTests\Models\User\Types\Password
    {
        return $this->password;
    }


}