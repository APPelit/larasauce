<?php

namespace APPelit\LaraSauce\MessageRepository;

use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotMessageRepository;
use EventSauce\EventSourcing\AggregateRootId;

interface MessageRepository extends SnapshotMessageRepository
{
    /**
     * @return \Generator|\EventSauce\EventSourcing\Message[]
     */
    public function retrieveEverything(): \Generator;

    /**
     * @param AggregateRootId $id
     * @return int|null
     */
    public function countAll(AggregateRootId $id): ?int;

    /**
     * @return int|null
     */
    public function countEverything(): ?int;
}
