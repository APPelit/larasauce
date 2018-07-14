<?php

namespace Tests\EventSauceTests\Models\User\Commands;

final class ChangeEmail
{
    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\EventSauceTests\Models\User\Types\Email
     */
    private $email;

    public function __construct(
        \Tests\EventSauceTests\Models\User\Types\UserIdType $userId,
        \Tests\EventSauceTests\Models\User\Types\Email $email
    ) {
        $this->userId = $userId;
        $this->email = $email;
    }

    public function userId(): \Tests\EventSauceTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function email(): \Tests\EventSauceTests\Models\User\Types\Email
    {
        return $this->email;
    }
}
