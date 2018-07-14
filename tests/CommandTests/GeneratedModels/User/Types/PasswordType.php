<?php

namespace Tests\CommandTests\GeneratedModels\User\Types;

use APPelit\LaraSauce\Types\Traits\ValueObject;
use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;

final class PasswordType implements ValueObjectContract
{
    use ValueObject;

    /** @var bool */
    protected $payload;

    /**
     * StringType constructor.
     * @param string $value
     * @param bool $payload
     */
    private function __construct(string $value, bool $payload = false)
    {
        $this->value = $value;
        $this->payload = $payload;
    }

    /**
     * @param mixed $value
     * @return static
     */
    public static function fromPayload($value)
    {
        return new self($value, true);
    }

    /**
     * @return string
     */
    public function toPayload()
    {
        /** @var \Illuminate\Contracts\Hashing\Hasher $hash */
        $hash = app('hash');

        if (!$this->payload) {
            return $hash->make($this->value);
        }

        return $this->value;
    }
}
