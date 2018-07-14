<?php

namespace APPelit\LaraSauce;

use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

final class AggregateRootConfig implements Arrayable
{
    /** @var bool */
    private $autogenerate;

    /** @var \Illuminate\Support\Collection|\EventSauce\EventSourcing\Consumer[] */
    private $consumers;

    /**
     * @var MessageDecorator|null
     */
    private $decorator;

    /**
     * @var array
     */
    private $definition;

    /**
     * @var MessageDispatcher|null
     */
    private $dispatcher;

    /**
     * @var MessageRepository
     */
    private $repository;
    /**
     * @var string
     */
    private $root;

    /**
     * AggregateRootConfig constructor.
     * @param string $root
     * @param \EventSauce\EventSourcing\MessageRepository $repository
     * @param \EventSauce\EventSourcing\MessageDispatcher|null $dispatcher
     * @param \EventSauce\EventSourcing\MessageDecorator|null $decorator
     * @param \Illuminate\Support\Collection|null $consumers
     * @param bool $autogenerate
     * @param array|null $definition
     */
    public function __construct(string $root,
                                MessageRepository $repository,
                                MessageDispatcher $dispatcher = null,
                                MessageDecorator $decorator = null,
                                Collection $consumers = null,
                                bool $autogenerate = false,
                                array $definition = null)
    {
        $this->root = $root;
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
        $this->decorator = $decorator;
        $this->consumers = $consumers ?? collect();
        $this->autogenerate = $autogenerate;
        $this->definition = $definition;
    }

    public function getAutogenerate()
    {
        return $this->autogenerate;
    }

    /**
     * @return \Illuminate\Support\Collection|\EventSauce\EventSourcing\Consumer[]
     */
    public function getConsumers(): Collection
    {
        return $this->consumers;
    }

    /**
     * @return \EventSauce\EventSourcing\MessageDecorator|null
     */
    public function getDecorator(): ?MessageDecorator
    {
        return $this->decorator;
    }

    /**
     * @return array|null
     */
    public function getDefinition(): ?array
    {
        return $this->definition;
    }

    /**
     * @return \EventSauce\EventSourcing\MessageDispatcher|null
     */
    public function getDispatcher(): ?MessageDispatcher
    {
        return $this->dispatcher;
    }

    /**
     * @return \EventSauce\EventSourcing\MessageRepository
     */
    public function getRepository(): MessageRepository
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'aggregateRootClassName' => $this->root,
            'messageRepository' => $this->repository,
            'dispatcher' => $this->dispatcher,
            'decorator' => $this->decorator,
        ];
    }
}
