<?php

namespace APPelit\LaraSauce\Types\Traits;

use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;

trait ValueObject
{
    /** @var mixed */
    protected $value;

    abstract protected function __construct($value);

    /**
     * @param mixed $value
     * @return static
     */
    public static function fromNative($value)
    {
        return new static($value);
    }

    /**
     * @param mixed $value
     * @return static
     */
    public static function fromPayload($value)
    {
        return static::fromNative($value);
    }

    /**
     * @param \APPelit\LaraSauce\Types\ValueObject $other
     * @return bool
     */
    public function sameValueAs(ValueObjectContract $other): bool {
        return get_class($this) === get_class($other) && $this->toString() === $other->toString();
    }

    /**
     * @return mixed
     */
    public function toPayload()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->toPayload();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
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
        return $this->toPayload();
    }
}
