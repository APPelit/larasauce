<?php

namespace Tests\CommandTests\Models\User\Commands;

final class ChangeName
{
    /**
     * @var \Tests\CommandTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\CommandTests\Models\User\Types\Name
     */
    private $name;

    public function __construct(
        \Tests\CommandTests\Models\User\Types\UserIdType $userId,
        \Tests\CommandTests\Models\User\Types\Name $name
    ) {
        $this->userId = $userId;
        $this->name = $name;
    }

    public function userId(): \Tests\CommandTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function name(): \Tests\CommandTests\Models\User\Types\Name
    {
        return $this->name;
    }


}