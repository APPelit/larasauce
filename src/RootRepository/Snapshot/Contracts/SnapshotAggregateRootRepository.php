<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot\Contracts;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\AggregateRootRepository;

interface SnapshotAggregateRootRepository extends AggregateRootRepository
{
    /**
     * Retrieve the aggregate root
     *
     * @param AggregateRootId $aggregateRootId
     * @param bool $full Rebuild the root from scratch
     * @return object
     */
    public function retrieve(AggregateRootId $aggregateRootId, bool $full = false): object;

    /**
     * Perform a snapshot of a specific root
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param bool $full
     * @return void
     */
    public function snapshot(AggregateRootId $aggregateRootId, bool $full = false);

    /**
     * Perform a snapshot of all roots
     *
     * @param bool $full
     * @return void
     */
    public function snapshotAll(bool $full = false);

    /**
     * Reset the snapshot of a specific root
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     */
    public function resetSnapshot(AggregateRootId $aggregateRootId);

    /**
     * Reset the snapshot of all roots
     */
    public function resetAllSnapshots();
}
