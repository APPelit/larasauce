<?php

namespace Tests\CommandTests\Models\User\Commands;

final class ChangeEmail
{
    /**
     * @var \Tests\CommandTests\Models\User\Types\Email
     */
    private $userId;

    /**
     * @var \Tests\CommandTests\Models\User\Types\Email
     */
    private $email;

    public function __construct(
        \Tests\CommandTests\Models\User\Types\UserIdType $userId,
        \Tests\CommandTests\Models\User\Types\Email $email
    ) {
        $this->userId = $userId;
        $this->email = $email;
    }

    public function userId(): \Tests\CommandTests\Models\User\Types\UserIdType
    {
        return $this->userId;
    }

    public function email(): \Tests\CommandTests\Models\User\Types\Email
    {
        return $this->email;
    }
}
