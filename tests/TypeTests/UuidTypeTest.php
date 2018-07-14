<?php

namespace Tests\TypeTests;

use APPelit\LaraSauce\Types\UuidType;
use APPelit\LaraSauce\Types\ValueObject;
use EventSauce\EventSourcing\AggregateRootId;
use Tests\TestCase;
use Tests\TypeTests\Types\UuidTypeObject;

class UuidTypeTest extends TestCase
{
    /** @var UuidType */
    private static $uuid;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        static::$uuid = static::$uuid ?? UuidTypeObject::create();
    }

    public function testUuidTypeImplementsAggregateRootId()
    {
        $this->assertInstanceOf(AggregateRootId::class, static::$uuid);
    }

    public function testUuidTypeImplementsValueObject()
    {
        $this->assertInstanceOf(ValueObject::class, static::$uuid);
    }

    public function testUuidToStringEqualsToPayload()
    {
        $this->assertEquals(static::$uuid->toString(), static::$uuid->toPayload());
    }

    public function testRestoredUuidEqualsOriginal()
    {
        $restoredUuid = UuidTypeObject::fromString($uuidString = static::$uuid->toString());
        $this->assertEquals($uuidString, $restoredUuid->toString());
    }

    public function testRestoredUuidEqualsOtherUuid()
    {
        $restoredUuid = UuidTypeObject::fromString($uuidString = static::$uuid->toString());
        $this->assertTrue(static::$uuid->sameValueAs($restoredUuid));
    }
}
