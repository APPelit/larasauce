<?php

namespace APPelit\LaraSauce;

use EventSauce\EventSourcing\Consumer;
use Illuminate\Contracts\Queue\ShouldQueue;

interface Reactor extends Consumer, ShouldQueue
{
}