<?php

namespace APPelit\LaraSauce\RootRepository\Snapshot\State;

use EventSauce\EventSourcing\AggregateRootId;

class StateSnapshot implements \JsonSerializable
{
    /** @var AggregateRootId */
    private $id;

    /** @var int */
    private $version;

    /** @var State */
    private $state;

    /**
     * @param array $data
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\State\StateSnapshot
     */
    public static function restore(array $data): StateSnapshot
    {
        /** @var AggregateRootId $id */
        $id = $data['id_class'];

        return new static($id::fromString($data['id']), $data['version'], State::createFromSnapshot($data['state']));
    }

    /**
     * @param \EventSauce\EventSourcing\AggregateRootId $id
     * @param int $version
     * @param \APPelit\LaraSauce\RootRepository\Snapshot\State\State $state
     */
    public function __construct(AggregateRootId $id, int $version, State $state)
    {
        $this->id = $id;
        $this->version = $version;
        $this->state = $state;
    }

    /**
     * @return \EventSauce\EventSourcing\AggregateRootId
     */
    public function getId(): AggregateRootId
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return \APPelit\LaraSauce\RootRepository\Snapshot\State\State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id_class' => get_class($this->id),
            'id' => $this->id->toString(),
            'version' => $this->version,
            'state' => $this->state,
        ];
    }
}
