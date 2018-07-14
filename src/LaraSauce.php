<?php

namespace APPelit\LaraSauce;

use Illuminate\Support\Facades\Facade;

/**
 * Class EventSauce
 * @package APPelit\LaraSauce
 *
 * @method static array roots()
 * @method static \APPelit\LaraSauce\AggregateRootConfig rootConfiguration(string $root)
 * @method static \EventSauce\EventSourcing\AggregateRootRepository root(string $class)
 * @method static void generateClasses(string $root, bool $explicit = false)
 */
final class LaraSauce extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lara-sauce';
    }
}
