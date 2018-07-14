<?php

namespace Tests\EventSauceTests\Models\User\Types;

use APPelit\LaraSauce\Types\Traits\ValueObject;
use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;
use Illuminate\Support\Facades\Hash;

final class Password implements ValueObjectContract
{
    use ValueObject;

    /**
     * StringType constructor.
     * @param string $value
     * @param bool $fromPayload
     */
    private function __construct(string $value, bool $fromPayload = false)
    {
        if (!$fromPayload) {
            $this->value = Hash::make($value);
        } else {
            $this->value = $value;
        }
    }

    /**
     * @param mixed $value
     * @return static
     */
    public static function fromPayload($value)
    {
        return new self($value, true);
    }
}
