<?php

namespace APPelit\LaraSauce;

use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\CodeGeneration\DefinitionGroup;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

class Factory implements RepositoryFactory
{
    /** @var Config */
    private $config;

    /** @var Container */
    private $container;

    /** @var AggregateRootConfig[] */
    private $configurations;

    /** @var AggregateRootRepository[] */
    private $repositories;

    /** @var \Illuminate\Support\Collection|Consumer[] */
    private $globalConsumers;

    /** @var \Illuminate\Support\Collection|MessageDecorator[] */
    private $globalDecorators;

    /** @var MessageDispatcher */
    private $globalDispatcher;

    /** @var string */
    private $globalRepository;

    /**
     * RepositoryFactory constructor.
     * @param Container $container
     * @param Config $config
     */
    public function __construct(Container $container, Config $config)
    {
        $this->config = $config;
        $this->container = $container;

        $this->setDefaultConsumers(...$this->resolveConsumers($config->get('lara-sauce.consumers', [])));
        $this->setDefaultDecorators(...$this->resolveDecorators($config->get('lara-sauce.decorators', [])));
        $this->setMessageDispatcher($config->get('lara-sauce.dispatcher', MessageDispatcher::class));
        $this->setMessageRepository($config->get('lara-sauce.repository', MessageRepository::class));
        $this->registerRoots();
    }

    /**
     * @param string $root
     * @param array $config
     */
    public function register(string $root, array $config)
    {
        $this->configurations[$root] = $this->parseConfig($root, $config);

        $this->repositories[$root] = $this->container->make(
            AggregateRootRepository::class,
            $this->configurations[$root]->toArray()
        );
    }

    /**
     * @param string $root
     * @return \EventSauce\EventSourcing\AggregateRootRepository
     */
    public function root(string $root): AggregateRootRepository
    {
        if (!isset($this->repositories[$root])) {
            throw new \InvalidArgumentException("Repository [{$root}] is not defined");
        }

        return $this->repositories[$root];
    }

    /**
     * @param string $root
     * @return \APPelit\LaraSauce\AggregateRootConfig
     */
    public function rootConfiguration(string $root): AggregateRootConfig
    {
        if (!isset($this->configurations[$root])) {
            throw new \InvalidArgumentException("Configuration [{$root}] is not defined");
        }

        return $this->configurations[$root];
    }

    /**
     * @return string[]
     */
    public function roots(): array
    {
        return $this->repositories ? array_keys($this->repositories) : [];
    }

    /**
     * @param array $config
     * @return \Illuminate\Support\Collection|\EventSauce\EventSourcing\Consumer[]
     */
    public function resolveConsumers(array $config): Collection
    {
        return collect($config)->map(function ($parameters, $consumer) {
            if (is_numeric($consumer)) {
                $consumer = $parameters;
                $parameters = [];
            }

            if ($consumer instanceof Consumer) {
                return $consumer;
            }

            return $this->resolveConsumer($consumer, $parameters);
        });
    }

    /**
     * @param string $consumer
     * @param array $parameters
     * @return \EventSauce\EventSourcing\Consumer
     */
    public function resolveConsumer(string $consumer, array $parameters = []): Consumer
    {
        return $this->container->make($consumer, $parameters);
    }

    /**
     * @param array $config
     * @return \Illuminate\Support\Collection|\EventSauce\EventSourcing\MessageDecorator[]
     */
    public function resolveDecorators(array $config): Collection
    {
        return collect($config)->map(function ($parameters, $decorator) {
            if (is_numeric($decorator)) {
                $decorator = $parameters;
                $parameters = [];
            }

            if ($decorator instanceof MessageDecorator) {
                return $decorator;
            }

            return $this->resolveDecorator($decorator, $parameters);
        });
    }

    /**
     * @param string $decorator
     * @param array $parameters
     * @return \EventSauce\EventSourcing\MessageDecorator
     */
    public function resolveDecorator(string $decorator, array $parameters = []): MessageDecorator
    {
        return $this->container->make($decorator, $parameters);
    }

    /**
     * @param string $dispatcher
     * @param \EventSauce\EventSourcing\Consumer[] $consumers
     * @return \EventSauce\EventSourcing\MessageDispatcher
     */
    public function resolveDispatcher(string $dispatcher, Consumer ...$consumers): MessageDispatcher
    {
        return $this->container->make($dispatcher, $consumers);
    }

    /**
     * @param string $repository
     * @param string|null $table
     * @param array $config
     * @return \EventSauce\EventSourcing\MessageRepository
     */
    public function resolveRepository(string $repository, string $table, array $config = []): MessageRepository
    {
        return $this->container->make($repository, array_merge($config, ['tableName' => $table]));
    }

    /**
     * @param \EventSauce\EventSourcing\Consumer[] $consumers
     * @return \APPelit\LaraSauce\Factory
     */
    public function setDefaultConsumers(Consumer ...$consumers): Factory
    {
        $this->globalConsumers = collect($consumers);

        return $this;
    }

    /**
     * @param \EventSauce\EventSourcing\MessageDecorator[] $decorators
     * @return \APPelit\LaraSauce\Factory
     */
    public function setDefaultDecorators(MessageDecorator ...$decorators): Factory
    {
        $this->globalDecorators = collect($decorators);

        return $this;
    }

    /**
     * @param string $dispatcher
     * @return \APPelit\LaraSauce\Factory
     */
    public function setMessageDispatcher(string $dispatcher): Factory
    {
        $this->globalDispatcher = $dispatcher;

        return $this;
    }

    /**
     * @param string $repository
     * @return \APPelit\LaraSauce\Factory
     */
    public function setMessageRepository(string $repository): Factory
    {
        $this->globalRepository = $repository;

        return $this;
    }

    /**
     * @param string $root
     * @param bool $explicit
     */
    public function generateClasses(string $root, bool $explicit = false)
    {
        $rootConfiguration = $this->rootConfiguration($root);

        if (!$explicit && !$rootConfiguration->getAutogenerate()) {
            return;
        }

        $definition = $rootConfiguration->getDefinition();

        if (!empty($definition)) {
            $definitionLoader = $definitionLoader ?? new ConfigDefinitionLoader;

            $definitionGroup = new DefinitionGroup;
            foreach ($this->config->get('lara-sauce.deserializers', []) as $type => $deserializer) {
                $definitionGroup->typeDeserializer($type, $deserializer);
            }

            foreach ($this->config->get('lara-sauce.serializers', []) as $type => $serializer) {
                $definitionGroup->typeSerializer($type, $serializer);
            }

            $dumper = new Psr4CodeDumper(
                $this->config->get('lara-sauce.output_path', app_path()),
                $definitionLoader->load($definition, $definitionGroup)
            );

            $dumper->dump();
        }
    }

    /**
     * @param string $root
     * @param array|null $config
     * @return \APPelit\LaraSauce\AggregateRootConfig
     */
    protected function parseConfig(string $root, array $config = null): AggregateRootConfig
    {
        $config = $config ?? $this->config->get("lara-sauce.{$root}");

        $decorators = $this->globalDecorators->concat(
            $this->resolveDecorators(array_get($config, 'decorators', []))
        )->toArray();

        $tableName = array_get($config, 'table', $this->generateTableName(
            $this->config->get('lara-sauce.database.table_prefix', 'lara_sauce_'),
            $root
        ));

        $repositoryConfig = array_merge(
            $this->config->get('lara-sauce.repository_config', []),
            array_get($config, 'repository_config', [])
        );

        if ($repository = array_get($config, 'repository')) {
            if (!($repository instanceof MessageRepository)) {
                $repository = $this->resolveRepository($repository, $tableName, $repositoryConfig);
            }
        } else {
            $repository = $this->resolveRepository($this->globalRepository, $tableName, $repositoryConfig);
        }

        $consumersCollection = $this->globalConsumers->concat(
            $this->resolveConsumers(array_get($config, 'consumers', []))
        );

        $consumers = $consumersCollection->toArray();

        if ($dispatcher = array_get($config, 'dispatcher')) {
            if (!($dispatcher instanceof MessageDispatcher)) {
                $dispatcher = $this->resolveDispatcher($dispatcher, ...$consumers);
            }
        } else {
            $dispatcher = $this->resolveDispatcher($this->globalDispatcher, ...$consumers);
        }

        return new AggregateRootConfig(
            $root,
            $repository,
            $dispatcher,
            new MessageDecoratorChain($this->container->make(DefaultHeadersDecorator::class), ...$decorators),
            $consumersCollection,
            array_get($config, 'autogenerate', true),
            array_get($config, 'definition')
        );
    }

    /**
     * @return void
     */
    protected function registerRoots()
    {
        foreach ($this->config->get('lara-sauce.roots', []) as $root => $config) {
            if (is_numeric($root)) {
                $root = $config;
                $config = [];
            }

            $this->register($root, $config);
        }
    }

    /**
     * @param $prefix
     * @param $root
     * @return string
     */
    protected function generateTableName(string $prefix, string $root): string
    {
        $hashedRoot = hash('sha1', $root);

        // Return the table name, limited to 53 characters (this leaves room for the "_snapshots" suffix if needed)
        return substr("{$prefix}{$hashedRoot}", 0, 53);
    }
}
