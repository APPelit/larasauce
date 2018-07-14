<?php

namespace Tests\CommandTests\Models\User\Commands;

final class ChangePassword
{
    /**
     * @var \Tests\CommandTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\CommandTests\Models\User\Types\Password
     */
    private $password;

    public function __construct(
        \Tests\CommandTests\Models\User\Types\UserIdType $userId,
        \Tests\CommandTests\Models\User\Types\Password $password
    ) {
        $this->userId = $userId;
        $this->password = $password;
    }

    public function userId(): \Tests\CommandTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function password(): \Tests\CommandTests\Models\User\Types\Password
    {
        return $this->password;
    }


}