<?php

namespace APPelit\LaraSauce\MessageDispatcher;

use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;

final class LaravelMessageDispatcher implements MessageDispatcher
{
    /** @var \Illuminate\Support\Collection|Consumer[] */
    private $asyncConsumers;

    /** @var \Illuminate\Support\Collection|Consumer[] */
    private $syncConsumers;

    /**
     * QueuedMessageDispatcher constructor.
     * @param Consumer[] $consumers
     */
    public function __construct(Consumer ...$consumers)
    {
        $consumers = collect($consumers);

        $this->asyncConsumers = $consumers->filter(function (Consumer $consumer) {
            return $consumer instanceof ShouldQueue;
        });

        $this->syncConsumers = $consumers->filter(function (Consumer $consumer) {
            return !($consumer instanceof ShouldQueue);
        });
    }

    /**
     * @param Message[] $messages
     */
    public function dispatch(Message ...$messages)
    {
        foreach ($messages as $message) {
            foreach ($this->asyncConsumers as $consumer) {
                MessageJob::dispatch($consumer, $message);
            }

            foreach ($this->syncConsumers as $consumer) {
                $consumer->handle($message);
            }
        }
    }
}
