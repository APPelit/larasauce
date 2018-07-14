<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot\Contracts;

use APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootId;

interface SnapshotAggregateRoot extends AggregateRoot
{
    /**
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot
     */
    public function getStateSnapshot(): StateSnapshot;

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param \Generator|\EventSauce\EventSourcing\Message[] $events
     * @param \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot|null $stateSnapshot
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot
     */
    public static function reconstituteFromSnapshot(AggregateRootId $aggregateRootId, \Generator $events, StateSnapshot $stateSnapshot = null): SnapshotAggregateRoot;
}
