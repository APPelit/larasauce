<?php

namespace APPelit\LaraSauce;

use APPelit\LaraSauce\Commands\GenerateClasses;
use APPelit\LaraSauce\Commands\RebuildProjection;
use APPelit\LaraSauce\Commands\RebuildProjections;
use APPelit\LaraSauce\Commands\Snapshot;
use APPelit\LaraSauce\MessageDispatcher\LaravelMessageDispatcher;
use APPelit\LaraSauce\MessageRepository\LaravelMessageRepository;
use APPelit\LaraSauce\RootRepository\Snapshot\Contracts\SnapshotAggregateRoot;
use APPelit\LaraSauce\RootRepository\Snapshot\SnapshottingAggregateRootRepository;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\ConstructingEventSerializer;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\EventSerializer;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\EventSourcing\Time\Clock;
use EventSauce\EventSourcing\Time\SystemClock;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class LaraSauceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/lara-sauce.php' => config_path('lara-sauce.php'),
            ]);

            $this->registerCommands();
        }

        $config = $this->app->make('config');

        $definitionLoader = null;
        if ($config->get('lara-sauce.autogenerate', true)) {
            $this->generateClasses();
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lara-sauce.php', 'lara-sauce');

        $this->registerBindings();
        $this->registerAliases();
    }

    public function provides()
    {
        return [
            'lara-sauce',
            'lara-sauce.aggregate_root_repository',
            'lara-sauce.class_name_inflector',
            'lara-sauce.event_serializer',
            'lara-sauce.message_serializer',
            'lara-sauce.message_repository',
            'lara-sauce.message_dispatcher',
        ];
    }

    protected function generateClasses()
    {
        $config = $this->app->make('config');

        foreach ($config->get('lara-sauce.roots', []) as $root => $rootConfig) {
            if (array_get($rootConfig, 'autogenerate', true) === false) {
                continue;
            }

            LaraSauce::generateClasses($root);
        }
    }

    protected function registerAliases()
    {
        $this->app->alias(RepositoryFactory::class, 'lara-sauce');
        $this->app->alias(AggregateRootRepository::class, 'lara-sauce.aggregate_root_repository');
        $this->app->alias(ClassNameInflector::class, 'lara-sauce.class_name_inflector');
        $this->app->alias(EventSerializer::class, 'lara-sauce.event_serializer');
        $this->app->alias(MessageSerializer::class, 'lara-sauce.message_serializer');
        $this->app->alias(MessageRepository::class, 'lara-sauce.message_repository');
        $this->app->alias(MessageDispatcher::class, 'lara-sauce.message_dispatcher');
    }

    protected function registerBindings()
    {
        $config = $this->app->make('config');

        $this->app->singleton(Clock::class, function (Container $app) use ($config) {
            $parameters = [
                'timeZone' => new \DateTimeZone($config->get('app.timezone'))
            ];

            if ($clock = $config->get('lara-sauce.clock')) {
                return $app->make($clock, $parameters);
            }

            return $app->make(SystemClock::class, $parameters);
        });

        $this->app->singleton(ClassNameInflector::class, function (Container $app) use ($config) {
            if ($inflector = $config->get('lara-sauce.class_name_inflector')) {
                return $app->make($inflector);
            }

            return $app->make(DotSeparatedSnakeCaseInflector::class);
        });

        $this->app->singleton(EventSerializer::class, function (Container $app) use ($config) {
            if ($serializer = $config->get('lara-sauce.event_serializer')) {
                return $app->make($serializer);
            }

            return $app->make(ConstructingEventSerializer::class);
        });

        $this->app->singleton(MessageSerializer::class, function (Container $app) use ($config) {
            if ($serializer = $config->get('lara-sauce.message_serializer')) {
                return $app->make($serializer);
            }

            return $app->make(ConstructingMessageSerializer::class);
        });

        $this->app->singleton(RepositoryFactory::class, Factory::class);

        $this->app->bind(AggregateRootRepository::class, function (Container $app, array $parameters) use ($config) {
            if ($builder = $config->get('lara-sauce.aggregate_root_repository')) {
                return $app->make($builder, $parameters);
            }

            if (is_subclass_of($parameters['aggregateRootClassName'], SnapshotAggregateRoot::class)) {
                return $app->make(SnapshottingAggregateRootRepository::class, $parameters);
            }

            return $app->make(ConstructingAggregateRootRepository::class, $parameters);
        });

        $this->app->bind(MessageRepository::class, function (Container $app, array $parameters) use ($config) {
            if ($repository = $config->get('lara-sauce.message_repository')) {
                return $app->make($repository, $parameters);
            }

            return $app->make(LaravelMessageRepository::class, $parameters);
        });

        $this->app->bind(MessageDispatcher::class, function (Container $app, array $consumers = []) use ($config) {
            if ($dispatcher = $config->get('lara-sauce.message_dispatcher')) {
                return new $dispatcher(...$consumers);
            }

            return new LaravelMessageDispatcher(...$consumers);
        });
    }

    protected function registerCommands()
    {
        $this->commands([
            GenerateClasses::class,
            RebuildProjection::class,
            RebuildProjections::class,
            Snapshot::class,
        ]);
    }
}
