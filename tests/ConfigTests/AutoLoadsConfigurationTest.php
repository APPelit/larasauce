<?php

namespace Tests\ConfigTests;

use APPelit\LaraSauce\AggregateRootConfig;
use APPelit\LaraSauce\LaraSauce;
use APPelit\LaraSauce\MessageDispatcher\LaravelMessageDispatcher;
use APPelit\LaraSauce\MessageRepository\LaravelMessageRepository;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AutoLoadsConfigurationTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('lara-sauce', [
            'autogenerate' => false,
            'roots' => [
                Models\User\User::class => [
                    'consumers' => [
                        Projections\UserProjection::class,
                        new Reactors\UserReactor,
                    ],
                    'decorators' => [
                        Decorators\DummyUserDecorator::class,
                    ],
                ],
            ],
            'consumers' => [
                Consumers\GlobalConsumer::class,
            ],
            'decorators' => [
                new Decorators\DummyDecorator,
            ]
        ]);
    }

    /**
     * Check that the roots defined in the configuration are loaded
     *
     * @return void
     */
    public function testRootsLoadedCorrectly()
    {
        $this->assertEquals([Models\User\User::class], LaraSauce::roots());
    }

    public function testRootHasCorrectType()
    {
        $root = LaraSauce::root(Models\User\User::class);
        $this->assertInstanceOf(AggregateRootRepository::class, $root);
    }

    public function testConfigurationHasCorrectType()
    {
        /** @var AggregateRootConfig $configuration */
        $configuration = LaraSauce::rootConfiguration(Models\User\User::class);
        $this->assertInstanceOf(AggregateRootConfig::class, $configuration);
        return $configuration;
    }

    public function testConfigurationHasCorrectRoot()
    {
        $this->assertEquals(Models\User\User::class, $this->testConfigurationHasCorrectType()->getRoot());
    }

    public function testConfigurationHasCorrectRepositoryType()
    {
        $this->assertInstanceOf(MessageRepository::class, $this->testConfigurationHasCorrectType()->getRepository());
    }

    public function testConfigurationHasCorrectRepositoryInstance()
    {
        $this->assertInstanceOf(LaravelMessageRepository::class, $this->testConfigurationHasCorrectType()->getRepository());
    }

    public function testConfigurationHasCorrectDispatcherType()
    {
        $this->assertInstanceOf(MessageDispatcher::class, $this->testConfigurationHasCorrectType()->getDispatcher());
    }

    public function testConfigurationHasCorrectDispatcherInstance()
    {
        $this->assertInstanceOf(LaravelMessageDispatcher::class, $this->testConfigurationHasCorrectType()->getDispatcher());
    }

    public function testConfigurationHasCorrectDecoratorType()
    {
        $this->assertInstanceOf(MessageDecorator::class, $this->testConfigurationHasCorrectType()->getDecorator());
    }

    public function testConfigurationHasCorrectDecoratorInstance()
    {
        $this->assertInstanceOf(MessageDecoratorChain::class, $this->testConfigurationHasCorrectType()->getDecorator());
    }

    public function testConfigurationHasCorrectConsumerType()
    {
        $this->assertInstanceOf(Collection::class, $this->testConfigurationHasCorrectType()->getConsumers());
    }

    public function testConfigurationConsumersContainsCorrectTypes()
    {
        $this->assertContainsOnlyInstancesOf(Consumer::class, $this->testConfigurationHasCorrectType()->getConsumers()->toArray());
    }

    public function testFactoryThrowsOnNonExistingRoot()
    {
        try {
            LaraSauce::root('ThisDoesNotExist');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $t);
        }
    }

    public function testFactoryThrowsOnNonExistingConfiguration()
    {
        try {
            LaraSauce::rootConfiguration('ThisDoesNotExist');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $t);
        }
    }
}
