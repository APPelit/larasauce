<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot;

use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot;

abstract class BaseSnapshotAggregateRoot implements SnapshotAggregateRoot
{
    use Traits\SnapshotAggregateRootBehaviour;
}
