<?php

namespace Tests\EventSauceTests\Models\User\Commands;

final class ChangeUsername
{
    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Username
     */
    private $username;

    public function __construct(
        \Tests\EventSauceTests\Models\User\Types\UserIdType $userId,
        \Tests\EventSauceTests\Models\User\Types\Username $username
    ) {
        $this->userId = $userId;
        $this->username = $username;
    }

    public function userId(): \Tests\EventSauceTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function username(): \Tests\EventSauceTests\Models\User\Types\Username
    {
        return $this->username;
    }


}