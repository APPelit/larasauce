<?php

namespace Tests\CommandTests\GeneratedModels\User\Types;

use APPelit\LaraSauce\Types\Traits\ValueObject;
use APPelit\LaraSauce\Types\ValueObject as ValueObjectContract;

final class NameType implements ValueObjectContract
{
    use ValueObject;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
