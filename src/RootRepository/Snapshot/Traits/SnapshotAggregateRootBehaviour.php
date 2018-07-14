<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot\Traits;

use EventSauce\EventSourcing\AggregateRootBehaviour\EventApplyingBehaviour;
use EventSauce\EventSourcing\AggregateRootBehaviour\EventRecordingBehaviour;

trait SnapshotAggregateRootBehaviour
{
    use SnapshotBehaviour, EventApplyingBehaviour, EventRecordingBehaviour;
}
