<?php

namespace APPelit\LaraSauce\Util;

use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

final class UuidGen
{
    /** @var UuidGen */
    private static $instance;

    /** @var UuidFactory */
    private $orderedTimeFactory;

    protected function __construct()
    {
        $this->orderedTimeFactory = new UuidFactory();
        $this->orderedTimeFactory->setCodec(new OrderedTimeCodec($this->orderedTimeFactory->getUuidBuilder()));
    }

    /**
     * @return UuidGen
     */
    protected static function getInstance(): UuidGen
    {
        if (!static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    protected function execute(int $version = null, ...$parameters): UuidInterface
    {
        $version = $version ?? config('lara-sauce.uuid_version', 4);

        switch ($version) {
            case 1:
                return $this->orderedTimeFactory->uuid1(...$parameters);
            case 3:
                return Uuid::uuid3(...$parameters);
            case 4:
                return Uuid::uuid4();
            case 5:
                return Uuid::uuid5(...$parameters);
            default:
                throw new \InvalidArgumentException("Unsupported UUID version");
        }
    }

    /**
     * @param int|null $version
     * @param array $parameters
     * @return UuidInterface
     */
    public static function generate(int $version = null, ...$parameters): UuidInterface
    {
        return static::getInstance()->execute($version, ...$parameters);
    }
}
