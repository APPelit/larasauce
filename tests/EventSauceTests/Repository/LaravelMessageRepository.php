<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16-6-18
 * Time: 20:23
 */

namespace Tests\EventSauceTests\Repository;


use EventSauce\EventSourcing\Message;

class LaravelMessageRepository extends \APPelit\LaraSauce\MessageRepository\LaravelMessageRepository
{
    /**
     * @var \object[]
     */
    private $lastCommit = [];

    /**
     * @return \object[]
     */
    public function lastCommit(): array
    {
        return $this->lastCommit;
    }

    public function purgeLastCommit()
    {
        $this->lastCommit = [];
    }

    /**
     * @param Message $message
     * @return array
     */
    protected function serializeMessage(Message $message): array
    {
        $this->lastCommit[] = $message->event();

        return parent::serializeMessage($message);
    }
}