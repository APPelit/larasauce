<?php

namespace Tests\EventSauceTests\Models\User\Types;

use APPelit\LaraSauce\Types\Traits\ValueObject;
use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;

final class Email implements ValueObjectContract
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
