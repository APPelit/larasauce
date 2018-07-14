<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot\Traits;

use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot;
use APPelit\LaraSauce\RootRepository\Snapshot\State\State;
use APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootId;

trait SnapshotBehaviour
{
    /** @var AggregateRootId */
    private $aggregateRootId;

    /** @var int */
    private $aggregateRootVersion = 0;

    /** @var State */
    private $state;

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot|null $stateSnapshot
     */
    public function __construct(AggregateRootId $aggregateRootId, StateSnapshot $stateSnapshot = null)
    {
        $this->aggregateRootId = $aggregateRootId;

        if ($stateSnapshot) {
            $this->aggregateRootVersion = $stateSnapshot->getVersion();
            $this->state = $stateSnapshot->getState();
        } else {
            $this->state = new State();
        }
    }

    /**
     * @return \EventSauce\EventSourcing\AggregateRootId
     */
    public function aggregateRootId(): AggregateRootId
    {
        return $this->aggregateRootId;
    }

    /**
     * @return int
     */
    public function aggregateRootVersion(): int
    {
        return $this->aggregateRootVersion;
    }

    /**
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot
     */
    public function getStateSnapshot(): StateSnapshot
    {
        return new StateSnapshot($this->aggregateRootId, $this->aggregateRootVersion, $this->state);
    }

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param \Generator|\EventSauce\EventSourcing\Message[] $events
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot
     */
    public static function reconstituteFromEvents(AggregateRootId $aggregateRootId, \Generator $events): AggregateRoot
    {
        /** @var SnapshotAggregateRoot|SnapshotAggregateRootBehaviour $aggregateRoot */
        $aggregateRoot = new static($aggregateRootId);

        /** @var object $event */
        foreach ($events as $event) {
            $aggregateRoot->apply($event);
            ++$aggregateRoot->aggregateRootVersion;
        }

        return $aggregateRoot;
    }

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param \Generator|\EventSauce\EventSourcing\Message[] $events
     * @param \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot|null $stateSnapshot
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot
     */
    public static function reconstituteFromSnapshot(AggregateRootId $aggregateRootId, \Generator $events, StateSnapshot $stateSnapshot = null): SnapshotAggregateRoot
    {
        /** @var SnapshotAggregateRoot|SnapshotAggregateRootBehaviour $aggregateRoot */
        $aggregateRoot = new static($aggregateRootId, $stateSnapshot);

        /** @var object $event */
        foreach ($events as $event) {
            $aggregateRoot->apply($event);
            ++$aggregateRoot->aggregateRootVersion;
        }

        return $aggregateRoot;
    }

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        return $this->state->{$name};
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return void
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        $this->state->{$name} = $value;
    }

    /**
     * @param object $event
     * @return void
     */
    abstract protected function apply(object $event);
}
