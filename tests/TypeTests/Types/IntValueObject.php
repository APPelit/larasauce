<?php

namespace Tests\TypeTests\Types;

use APPelit\LaraSauce\Types\Traits\ValueObject;
use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;

class IntValueObject implements ValueObjectContract
{
    use ValueObject;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}