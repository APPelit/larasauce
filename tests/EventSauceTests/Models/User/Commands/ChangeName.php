<?php

namespace Tests\EventSauceTests\Models\User\Commands;

final class ChangeName
{
    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Name
     */
    private $name;

    public function __construct(
        \Tests\EventSauceTests\Models\User\Types\UserIdType $userId,
        \Tests\EventSauceTests\Models\User\Types\Name $name
    ) {
        $this->userId = $userId;
        $this->name = $name;
    }

    public function userId(): \Tests\EventSauceTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function name(): \Tests\EventSauceTests\Models\User\Types\Name
    {
        return $this->name;
    }


}