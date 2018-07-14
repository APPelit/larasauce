<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot\Contracts;

use APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\MessageRepository;

interface SnapshotMessageRepository extends MessageRepository
{
    /**
     * @param \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot $stateSnapshot
     * @return void
     */
    public function persistState(StateSnapshot $stateSnapshot);

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $rootId
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot|null
     */
    public function retrieveState(AggregateRootId $rootId): ?StateSnapshot;

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $rootId
     * @param int $version
     * @return \Generator|\EventSauce\EventSourcing\Message[]
     */
    public function retrieveAfter(AggregateRootId $rootId, int $version): \Generator;

    /**
     * @return \Generator|\EventSauce\EventSourcing\AggregateRootId[]
     */
    public function getAllRoots(): \Generator;

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $rootId
     * @return void
     */
    public function resetState(AggregateRootId $rootId);
}
