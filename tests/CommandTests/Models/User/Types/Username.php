<?php

namespace Tests\CommandTests\Models\User\Types;

use APPelit\LaraSauce\Types\Traits\ValueObject;
use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;

final class Username implements ValueObjectContract
{
    use ValueObject;

    /**
     * StringType constructor.
     * @param string $value
     */
    protected function __construct(string $value)
    {
        $this->value = $value;
    }
}
