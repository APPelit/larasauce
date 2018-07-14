<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot;

use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot;
use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRootRepository;
use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotMessageRepository;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;

class SnapshottingAggregateRootRepository implements SnapshotAggregateRootRepository
{
    /** @var string */
    private $aggregateRootClassName;

    /** @var SnapshotMessageRepository */
    private $repository;

    /** @var MessageDispatcher */
    private $dispatcher;

    /** @var MessageDecorator */
    private $decorator;

    /**
     * @param string $aggregateRootClassName
     * @param \APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotMessageRepository $messageRepository
     * @param \EventSauce\EventSourcing\MessageDispatcher|null $dispatcher
     * @param \EventSauce\EventSourcing\MessageDecorator|null $decorator
     */
    public function __construct(
        string $aggregateRootClassName,
        SnapshotMessageRepository $messageRepository,
        MessageDispatcher $dispatcher = null,
        MessageDecorator $decorator = null
    )
    {
        $this->aggregateRootClassName = $aggregateRootClassName;
        $this->repository = $messageRepository;
        $this->dispatcher = $dispatcher ?: new SynchronousMessageDispatcher();
        $this->decorator = $decorator ?: new DefaultHeadersDecorator();
    }

    /**
     * Retrieve the aggregate root
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param bool $full
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot
     */
    public function retrieve(AggregateRootId $aggregateRootId, bool $full = false): object
    {
        /** @var SnapshotAggregateRoot $className */
        $className = $this->aggregateRootClassName;

        if ($full) {
            $events = $this->retrieveAllEvents($aggregateRootId);

            return $className::reconstituteFromEvents($aggregateRootId, $events);
        }

        $stateSnapshot = $this->repository->retrieveState($aggregateRootId);

        $version = 0;
        if ($stateSnapshot) {
            $version = $stateSnapshot->getVersion();
        }

        $events = $this->retrieveAllEventsAfter($aggregateRootId, $version);

        return $className::reconstituteFromSnapshot($aggregateRootId, $events, $stateSnapshot);
    }

    /**
     * Retrieve the events following a state snapshot
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @return \Generator|\EventSauce\EventSourcing\Message[]
     */
    private function retrieveAllEvents(AggregateRootId $aggregateRootId): \Generator
    {
        /** @var Message $message */
        foreach ($this->repository->retrieveAll($aggregateRootId) as $message) {
            yield $message->event();
        }
    }

    /**
     * Retrieve the events following a state snapshot
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param int $version
     * @return \Generator|\EventSauce\EventSourcing\Message[]
     */
    private function retrieveAllEventsAfter(AggregateRootId $aggregateRootId, int $version): \Generator
    {
        /** @var Message $message */
        foreach ($this->repository->retrieveAfter($aggregateRootId, $version) as $message) {
            yield $message->event();
        }
    }

    /**
     * Persist the aggregate root
     *
     * @param \EventSauce\EventSourcing\AggregateRoot|object $aggregateRoot
     */
    public function persist(object $aggregateRoot)
    {
        assert($aggregateRoot instanceof AggregateRoot, 'Expected $aggregateRoot to be an instance of ' . AggregateRoot::class);

        $this->persistEvents(
            $aggregateRoot->aggregateRootId(),
            $aggregateRoot->aggregateRootVersion(),
            ...$aggregateRoot->releaseEvents()
        );
    }

    /**
     * Persist the given events for the specified root
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param int $aggregateRootVersion
     * @param object ...$events
     */
    public function persistEvents(AggregateRootId $aggregateRootId, int $aggregateRootVersion, object ...$events)
    {
        $metadata = [Header::AGGREGATE_ROOT_ID => $aggregateRootId];
        $messages = array_map(function (object $event) use ($metadata, &$aggregateRootVersion) {
            return $this->decorator->decorate(new Message(
                $event,
                $metadata + [Header::AGGREGATE_ROOT_VERSION => ++$aggregateRootVersion]
            ));
        }, $events);

        $this->repository->persist(...$messages);
        $this->dispatcher->dispatch(...$messages);
    }

    /**
     * Perform a snapshot of a specific root
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     * @param bool $full
     */
    public function snapshot(AggregateRootId $aggregateRootId, bool $full = false)
    {
        $root = $this->retrieve($aggregateRootId, $full);

        $this->repository->persistState($root->getStateSnapshot());
    }

    /**
     * Perform a snapshot of all roots
     *
     * @param bool $full
     */
    public function snapshotAll(bool $full = false)
    {
        $roots = $this->repository->getAllRoots();

        if (!empty($roots)) {
            foreach ($roots as $root) {
                $this->snapshot($root, $full);
            }
        }
    }

    /**
     * Reset the snapshot of a specific root
     *
     * @param \EventSauce\EventSourcing\AggregateRootId $aggregateRootId
     */
    public function resetSnapshot(AggregateRootId $aggregateRootId)
    {
        $this->repository->resetState($aggregateRootId);
    }

    /**
     * Reset the snapshot of all roots
     */
    public function resetAllSnapshots()
    {
        $roots = $this->repository->getAllRoots();

        if (!empty($roots)) {
            foreach ($roots as $root) {
                $this->resetSnapshot($root);
            }
        }
    }
}
