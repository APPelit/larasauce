<?php

namespace APPelit\LaraSauce\MessageRepository;

use APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot;
use APPelit\LaraSauce\Util\LaraSauceMigration;
use APPelit\LaraSauce\Util\UuidGen;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class LaravelMessageRepository implements MessageRepository
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $snapshotTable;

    /**
     * @var ClassNameInflector
     */
    private $classNameInflector;

    /**
     * @var bool
     */
    private $useBinary;

    /**
     * SingleTableMessageRepository constructor.
     * @param Container $container
     * @param MessageSerializer $serializer
     * @param string $tableName
     * @param string|null $snapshotTableName
     * @param \EventSauce\EventSourcing\ClassNameInflector|null $classNameInflector
     */
    public function __construct(
        Container $container,
        MessageSerializer $serializer,
        string $tableName,
        string $snapshotTableName = null,
        ClassNameInflector $classNameInflector = null
    )
    {
        $this->serializer = $serializer;
        $this->container = $container;
        $this->table = $tableName;
        $this->snapshotTable = $snapshotTableName ?: "{$tableName}_snapshots";
        $this->classNameInflector = $classNameInflector ?: new DotSeparatedSnakeCaseInflector();

        $this->useBinary = config('lara-sauce.binary_uuid') ?: false;

        try {
            LaraSauceMigration::messageRepository($this->table);
            LaraSauceMigration::snapshotRepository($this->snapshotTable);
        } catch (\Illuminate\Database\QueryException $e) {
            if (!app()->runningInConsole()) {
                throw $e;
            }
        }
    }

    /**
     * @param Message[] $messages
     * @throws \Throwable
     */
    public function persist(Message ...$messages)
    {
        $db = $this->getDatabase();
        $table = $db->table($this->table)->useWritePdo();

        $db->transaction(function () use ($table, $messages) {
            foreach ($this->serializeMessages(...$messages) as $message) {
                $table->insert($message);
            }
        });
    }

    /**
     * @param AggregateRootId $id
     * @return \Generator|\EventSauce\EventSourcing\Message[]
     * @throws \Exception
     */
    public function retrieveAll(AggregateRootId $id): \Generator
    {
        $aggregateRootId = $id->toString();
        if ($this->useBinary) {
            $aggregateRootId = Uuid::fromString($aggregateRootId)->getBytes();
        }

        $query = $this->getDatabase()
            ->table($this->table)
            ->select('payload')
            ->where('aggregate_root_id', $aggregateRootId)
            ->where('aggregate_root_type', $this->classNameInflector->instanceToType($id))
            ->orderBy('time_of_recording', 'ASC');

        if ($query->count() == 0) {
            return;
        }

        $page = 1;
        $count = config('lara-sauce.database.chunk_size', 1000);

        do {
            $messages = $query->forPage($page, $count)->get();

            $countResults = $messages->count();
            if ($countResults == 0) {
                break;
            }

            foreach ($messages as $message) {
                yield from $this->serializer->unserializePayload(json_decode($message->payload, true));
            }

            unset($messages);

            $page++;
        } while ($countResults == $count);
    }

    /**
     * @return \Generator|\EventSauce\EventSourcing\Message[]
     */
    public function retrieveEverything(): \Generator
    {
        $query = $this->getDatabase()
            ->table($this->table)
            ->select('payload')
            ->orderBy('time_of_recording', 'ASC');

        if ($query->count() == 0) {
            return;
        }

        $page = 1;
        $count = config('lara-sauce.database.chunk_size', 1000);

        do {
            $messages = $query->forPage($page, $count)->get();

            $countResults = $messages->count();
            if ($countResults == 0) {
                break;
            }

            foreach ($messages as $message) {
                yield from $this->serializer->unserializePayload(json_decode($message->payload, true));
            }

            unset($messages);

            $page++;
        } while ($countResults == $count);
    }

    /**
     * @param AggregateRootId $id
     * @return int|null
     */
    public function countAll(AggregateRootId $id): ?int
    {
        $aggregateRootId = $id->toString();
        if ($this->useBinary) {
            $aggregateRootId = Uuid::fromString($aggregateRootId)->getBytes();
        }

        return $this->getDatabase()
            ->table($this->table)
            ->where('aggregate_root_id', $aggregateRootId)
            ->where('aggregate_root_type', $this->classNameInflector->instanceToType($id))
            ->count();
    }

    /**
     * @return int|null
     */
    public function countEverything(): ?int
    {
        return $this->getDatabase()
            ->table($this->table)
            ->count();
    }

    /**
     * @return Connection
     */
    protected function getDatabase(): Connection
    {
        $db = $this->container->make('db');

        if ($name = config('lara-sauce.database.connection')) {
            return $db->connection($name);
        }

        return $db->connection();
    }

    /**
     * @param Message[] $messages
     * @return \Generator|array[]
     */
    protected function serializeMessages(Message ...$messages): \Generator
    {
        foreach ($messages as $message) {
            yield $this->serializeMessage($message);
        }
    }

    /**
     * @param Message $message
     * @return array
     */
    protected function serializeMessage(Message $message): array
    {
        $payload = $this->serializer->serializeMessage($message);

        $id = UuidGen::generate(1);
        $id = $this->useBinary ? $id->getBytes() : $id->toString();

        $aggregateRootId = $payload['headers'][Header::AGGREGATE_ROOT_ID] ?? null;
        if ($aggregateRootId) {
            $aggregateRootId = Uuid::fromString($aggregateRootId);
            $aggregateRootId = $this->useBinary ? $aggregateRootId->getBytes() : $aggregateRootId->toString();
        }

        $eventId = $payload['headers'][Header::EVENT_ID] ?? null;
        $eventId = $eventId ? Uuid::fromString($eventId) : UuidGen::generate();
        $eventId = $this->useBinary ? $eventId->getBytes() : $eventId->toString();

        return [
            'id' => $id,
            'time_of_recording' => $payload['headers'][Header::TIME_OF_RECORDING],
            'aggregate_root_id' => $aggregateRootId,
            'aggregate_root_type' => $payload['headers'][Header::AGGREGATE_ROOT_ID_TYPE] ?? null,
            'aggregate_root_version' => $payload['headers'][Header::AGGREGATE_ROOT_VERSION] ?? 0,
            'event_id' => $eventId,
            'event_type' => $payload['headers'][Header::EVENT_TYPE],
            'payload' => json_encode($payload),
        ];
    }

    /**
     * @param \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot $stateSnapshot
     * @return void
     * @throws \Throwable
     */
    public function persistState(StateSnapshot $stateSnapshot)
    {
        $db = $this->getDatabase();
        $table = $db->table($this->snapshotTable)->useWritePdo();

        $db->transaction(function () use ($table, $stateSnapshot) {
            $rootId = $stateSnapshot->getId();

            $aggregateRootId = $rootId->toString();
            if ($this->useBinary) {
                $aggregateRootId = Uuid::fromString($aggregateRootId)->getBytes();
            }

            $idType = $this->classNameInflector->instanceToType($rootId);

            $query = $table
                ->where('aggregate_root_id', $aggregateRootId)
                ->where('aggregate_root_type', $idType);

            if ($query->count()) {
                $query->update([
                    'version' => $stateSnapshot->getVersion(),
                    'state' => json_encode($stateSnapshot->getState()),
                ]);
            } else {
                $table->insert([
                    'aggregate_root_id' => $aggregateRootId,
                    'aggregate_root_type' => $idType,
                    'version' => $stateSnapshot->getVersion(),
                    'state' => json_encode($stateSnapshot->getState()),
                ]);
            }
        });
    }

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $rootId
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot
     */
    public function retrieveState(AggregateRootId $rootId): ?StateSnapshot
    {
        $aggregateRootId = $rootId->toString();
        if ($this->useBinary) {
            $aggregateRootId = Uuid::fromString($aggregateRootId)->getBytes();
        }

        $data = $this->getDatabase()
            ->table($this->snapshotTable)
            ->where('aggregate_root_id', $aggregateRootId)
            ->where('aggregate_root_type', $this->classNameInflector->instanceToType($rootId))
            ->first();

        if (!$data || !$data->version || !($state = json_decode($data->state))) {
            return null;
        }

        return new StateSnapshot($rootId, $data->version, $state);
    }

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $rootId
     * @param int $version
     * @return \Generator|\EventSauce\EventSourcing\Message[]
     */
    public function retrieveAfter(AggregateRootId $rootId, int $version): \Generator
    {
        $aggregateRootId = $rootId->toString();
        if ($this->useBinary) {
            $aggregateRootId = Uuid::fromString($aggregateRootId)->getBytes();
        }

        $query = $this->getDatabase()
            ->table($this->table)
            ->select('payload')
            ->where('aggregate_root_id', $aggregateRootId)
            ->where('aggregate_root_type', $this->classNameInflector->instanceToType($rootId))
            ->where('aggregate_root_version', '>', $version)
            ->orderBy('time_of_recording', 'ASC');

        $page = 1;
        $count = config('lara-sauce.database.chunk_size', 1000);

        do {
            $messages = $query->forPage($page, $count)->get();

            $countResults = $messages->count();
            if ($countResults == 0) {
                break;
            }

            foreach ($messages as $message) {
                yield from $this->serializer->unserializePayload(json_decode($message->payload, true));
            }

            unset($messages);

            $page++;
        } while ($countResults == $count);
    }

    /**
     * @return \Generator|\EventSauce\EventSourcing\AggregateRootId[]
     */
    public function getAllRoots(): \Generator
    {
        /** @var Collection|\stdClass[] $ids */
        $ids = $this->getDatabase()
            ->table($this->table)
            ->select(['aggregate_root_id', 'aggregate_root_type'])
            ->groupBy(['aggregate_root_id', 'aggregate_root_type'])
            ->orderBy('aggregate_root_type')
            ->orderBy('aggregate_root_id')
            ->get();

        foreach ($ids as $id) {
            if ($id->aggregate_root_id && $id->aggregate_root_type) {
                /** @var AggregateRootId $aggregateRootIdClassName */
                $aggregateRootIdClassName = $this->classNameInflector->typeToClassName($id->aggregate_root_type);

                $aggregateRootId = $id->aggregate_root_id;
                if ($this->useBinary) {
                    $aggregateRootId = Uuid::fromBytes($aggregateRootId)->toString();
                }

                yield $aggregateRootIdClassName::fromString($aggregateRootId);
            }
        }
    }

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $rootId
     * @return void
     * @throws \Throwable
     */
    public function resetState(AggregateRootId $rootId)
    {
        $db = $this->getDatabase();
        $table = $db->table($this->snapshotTable)->useWritePdo();

        $db->transaction(function () use ($table, $rootId) {
            $aggregateRootId = $rootId->toString();
            if ($this->useBinary) {
                $aggregateRootId = Uuid::fromString($aggregateRootId)->getBytes();
            }

            $table
                ->where('aggregate_root_id', $aggregateRootId)
                ->where('aggregate_root_type', $this->classNameInflector->instanceToType($rootId))
                ->delete();
        });
    }
}
