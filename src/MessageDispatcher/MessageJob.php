<?php

namespace APPelit\LaraSauce\MessageDispatcher;

use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class MessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Consumer
     */
    private $consumer;

    /**
     * @var Message
     */
    private $message;

    /**
     * QueueJob constructor.
     * @param Consumer $consumer
     * @param Message $message
     */
    public function __construct(Consumer $consumer, Message $message)
    {
        $this->consumer = $consumer;
        $this->message = $message;
    }

    public function handle()
    {
        $this->consumer->handle($this->message);
    }
}
