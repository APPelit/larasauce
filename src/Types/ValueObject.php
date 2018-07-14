<?php

namespace APPelit\LaraSauce\Types;

interface ValueObject extends \JsonSerializable
{
    /**
     * @param mixed $value
     * @return static
     */
    public static function fromNative($value);

    /**
     * @param mixed $value
     * @return static
     */
    public static function fromPayload($value);

    /**
     * @return mixed
     */
    public function toPayload();

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @param ValueObject $other
     * @return bool
     */
    public function sameValueAs(ValueObject $other): bool;
}