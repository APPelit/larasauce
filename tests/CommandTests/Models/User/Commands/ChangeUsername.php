<?php

namespace Tests\CommandTests\Models\User\Commands;

final class ChangeUsername
{
    /**
     * @var \Tests\CommandTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\CommandTests\Models\User\Types\Username
     */
    private $username;

    public function __construct(
        \Tests\CommandTests\Models\User\Types\UserIdType $userId,
        \Tests\CommandTests\Models\User\Types\Username $username
    ) {
        $this->userId = $userId;
        $this->username = $username;
    }

    public function userId(): \Tests\CommandTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function username(): \Tests\CommandTests\Models\User\Types\Username
    {
        return $this->username;
    }


}