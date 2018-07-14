<?php

namespace Tests\EventSauceTests;

use APPelit\LaraSauce\MessageDispatcher\LaravelMessageDispatcher;
use APPelit\LaraSauce\Util\UuidGen;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\AggregateRootTestCase;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\TestUtilities\ConsumerThatSerializesMessages;
use EventSauce\EventSourcing\UuidAggregateRootId;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Foundation\Testing\Concerns\MocksApplicationServices;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Orchestra\Testbench\Concerns\Testing;
use Orchestra\Testbench\Contracts\TestCase as OrchestraTestCase;

abstract class TestCase extends AggregateRootTestCase implements OrchestraTestCase
{
    use Testing,
        InteractsWithAuthentication,
        InteractsWithConsole,
        InteractsWithContainer,
        InteractsWithDatabase,
        InteractsWithExceptionHandling,
        InteractsWithSession,
        MakesHttpRequests,
        MocksApplicationServices;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->setUpTheTestEnvironment();

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->artisan('migrate', ['--database' => 'testbench']);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->tearDownTheTestEnvironment();
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        return $this->setUpTheTestEnvironmentTraits();
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        $this->app = $this->createApplication();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return [
            \APPelit\LaraSauce\LaraSauceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @return array
     */
    protected function getPackageAliases()
    {
        return [
            'LaraSauce' => \APPelit\LaraSauce\LaraSauce::class
        ];
    }

    protected function newAggregateRootId(): AggregateRootId {
        static $id;

        if (!$id) {
            $id = new UuidAggregateRootId(UuidGen::generate(4)->toString());
        }

        return $id;
    }

    protected function aggregateRootClassName(): string {
        return 'user';
    }



    protected function messageDispatcher(): MessageDispatcher
    {
        return new LaravelMessageDispatcher(
            new ConsumerThatSerializesMessages(),
            ...$this->consumers()
        );
    }
}
