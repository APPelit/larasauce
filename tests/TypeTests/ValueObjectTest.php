<?php

namespace Tests\TypeTests;

use APPelit\LaraSauce\Types\ValueObject;
use Tests\TestCase;
use Tests\TypeTests\Types\BasicValueObject;
use Tests\TypeTests\Types\IntValueObject;

class ValueObjectTest extends TestCase
{
    /** @var ValueObject */
    private static $firstValue;

    /** @var ValueObject */
    private static $secondValue;

    /** @var ValueObject */
    private static $thirdValue;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        static::$firstValue = static::$firstValue ?? new BasicValueObject('testValue');
        static::$secondValue = static::$secondValue ?? new BasicValueObject(1);
        static::$thirdValue = static::$thirdValue ?? new IntValueObject(1);
    }

    public function testFirstValueInstanceHasCorrectType()
    {
        $this->assertInstanceOf(ValueObject::class, static::$firstValue);
    }

    public function testFirstValueContainsCorrectValue()
    {
        $this->assertEquals('testValue', static::$firstValue->toPayload());
    }

    public function testFirstValueReturnsSameValueForToStringAndToPayload()
    {
        $this->assertTrue(static::$firstValue->toString() === static::$firstValue->toPayload());
    }

    public function testFirstValueIsTheSameAsItself()
    {
        $this->assertTrue(static::$firstValue->sameValueAs(static::$firstValue));
    }

    public function testFirstValueIsNotTheSameAsSecondValue()
    {
        $this->assertFalse(static::$firstValue->sameValueAs(static::$secondValue));
    }

    public function testFirstValueIsNotTheSameAsThirdValue()
    {
        $this->assertFalse(static::$firstValue->sameValueAs(static::$thirdValue));
    }

    public function testRestoringFirstValueResultsInTheSameValue()
    {
        $this->assertTrue(($payload = static::$firstValue->toPayload()) === BasicValueObject::fromPayload($payload)->toPayload());
    }

    public function testSecondValueInstanceHasCorrectType()
    {
        $this->assertInstanceOf(ValueObject::class, static::$secondValue);
    }

    public function testSecondValueReturnsStringFromToString()
    {
        $this->assertTrue(is_string(static::$secondValue->toString()));
    }

    public function testSecondValueReturnsIntFromToPayload()
    {
        $this->assertTrue(is_int(static::$secondValue->toPayload()));
    }

    public function testSecondValueIsTheSameAsItself()
    {
        $this->assertTrue(static::$secondValue->sameValueAs(static::$secondValue));
    }

    public function testRestoringSecondValueResultsInTheSameValue()
    {
        $this->assertTrue(($payload = static::$secondValue->toPayload()) === BasicValueObject::fromPayload($payload)->toPayload());
    }

    public function testSecondValueIsNotTheSameAsFirstValue()
    {
        $this->assertFalse(static::$secondValue->sameValueAs(static::$firstValue));
    }

    public function testSecondValueIsNotTheSameAsThirdValue()
    {
        $this->assertFalse(static::$secondValue->sameValueAs(static::$thirdValue));
    }

    public function testThirdValueInstanceHasCorrectType()
    {
        $this->assertInstanceOf(ValueObject::class, static::$thirdValue);
    }

    public function testThirdValueIsTheSameAsItself()
    {
        $this->assertTrue(static::$thirdValue->sameValueAs(static::$thirdValue));
    }

    public function testRestoringThirdValueResultsInTheSameValue()
    {
        $this->assertTrue(($payload = static::$thirdValue->toPayload()) === IntValueObject::fromPayload($payload)->toPayload());
    }

    public function testThirdValueIsNotTheSameAsFirstValue()
    {
        $this->assertFalse(static::$thirdValue->sameValueAs(static::$firstValue));
    }

    public function testThirdValueIsNotTheSameAsSecondValue()
    {
        $this->assertFalse(static::$thirdValue->sameValueAs(static::$secondValue));
    }

    public function testIntValueObjectThrowsWhenConstructedWithString()
    {
        try {
            new IntValueObject('test');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\TypeError::class, $t);
        }
    }

    public function testIntValueObjectThrowsWhenFromNativeIsString()
    {
        try {
            IntValueObject::fromNative('test');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\TypeError::class, $t);
        }
    }

    public function testIntValueObjectThrowsWhenRestoredFromString()
    {
        try {
            IntValueObject::fromPayload('test');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\TypeError::class, $t);
        }
    }
}
