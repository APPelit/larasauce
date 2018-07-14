<?php

namespace APPelit\LaraSauce\Types;

use APPelit\LaraSauce\Util\UuidGen;
use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class UuidType implements AggregateRootId, ValueObject
{
    use Traits\ValueObject;

    /**
     * @return UuidType
     */
    public static function create(): UuidType
    {
        return new static(UuidGen::generate());
    }

    /**
     * @param string $value
     * @return static
     */
    public static function fromNative($value)
    {
        return new static(Uuid::fromString($value));
    }

    /**
     * @param string $aggregateRootId
     *
     * @return static
     */
    public static function fromString(string $aggregateRootId): AggregateRootId
    {
        return static::fromNative($aggregateRootId);
    }

    private function __construct(UuidInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function toPayload()
    {
        return $this->value->toString();
    }
}
