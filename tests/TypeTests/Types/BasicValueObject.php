<?php

namespace Tests\TypeTests\Types;

use APPelit\LaraSauce\Types\Traits\ValueObject;
use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;

class BasicValueObject implements ValueObjectContract
{
    use ValueObject;

    public function __construct($value)
    {
        $this->value = $value;
    }
}