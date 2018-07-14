<?php

namespace Tests\CommandTests;

use APPelit\LaraSauce\LaraSauce;
use EventSauce\EventSourcing\InMemoryMessageRepository;
use Tests\TestCase;

class RebuildCommandsTest extends TestCase
{
    /** @var Models\User\Types\UserIdType */
    private $userId;

    /** @var string */
    private $expectedResult;

    protected function setUp()
    {
        parent::setUp();

        $repository = LaraSauce::root(Models\User\User::class);

        /** @var Models\User\User $user */
        $user = $repository->retrieve($this->userId = Models\User\Types\UserIdType::create());

        $user->performRegisterUser(new Models\User\Commands\RegisterUser(
            Models\User\Types\Name::fromNative('test'),
            Models\User\Types\Email::fromNative('test@example.com'),
            Models\User\Types\Username::fromNative('test'),
            Models\User\Types\Password::fromNative('password')
        ));

        $user->performChangeName(new Models\User\Commands\ChangeName(
            $this->userId,
            Models\User\Types\Name::fromNative('test2')
        ));

        $user->performChangeEmail(new Models\User\Commands\ChangeEmail(
            $this->userId,
            Models\User\Types\Email::fromNative('test2@example.com')
        ));

        $user->performChangePassword(new Models\User\Commands\ChangePassword(
            $this->userId,
            Models\User\Types\Password::fromNative('testtest')
        ));

        $user->performChangeEmail(new Models\User\Commands\ChangeEmail(
            $this->userId,
            Models\User\Types\Email::fromNative('test@example.com')
        ));

        $repository->persist($user);

        $this->expectedResult = json_encode($this->app['db']->table('command_test_users')->get());
    }

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
                    'table' => 'command_test',
                    'consumers' => [
                        Projections\UserProjection::class,
                        new Reactors\UserReactor,
                    ],
                ],
                Models\Dummy\Dummy::class => [
                    'repository' => new InMemoryMessageRepository,
                ],
                Models\Dummy\Dummy2::class => [
                    'repository' => new InMemoryMessageRepository,
                    'consumers' => [
                        Projections\UserProjection::class,
                    ]
                ],
            ],
        ]);
    }

    public function testUserIsCreated()
    {
        $this->assertNotNull($this->app['db']->table('command_test_users')->where('id', $this->userId)->first());
    }

    public function testRebuildProjectionsWithoutRoots()
    {
        $this->artisan('lara-sauce:rebuild_projections', ['--force' => true]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }

    public function testRebuildProjectionsWithUserRoot()
    {
        $this->artisan('lara-sauce:rebuild_projections', ['--force' => true, 'roots' => Models\User\User::class]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }

    public function testRebuildProjectionWithoutRoots()
    {
        $this->artisan('lara-sauce:rebuild_projection', ['--force' => true, 'projection' => Projections\UserProjection::class]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }

    public function testRebuildProjectionWithUserRoot()
    {
        $this->artisan('lara-sauce:rebuild_projection', ['--force' => true, 'projection' => Projections\UserProjection::class, 'roots' => Models\User\User::class]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }

    public function testRebuildProjectionWithNonRebuildableProjectionAndWithoutRoot()
    {
        $this->artisan('lara-sauce:rebuild_projection', ['--force' => true, 'projection' => Reactors\UserReactor::class]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }

    public function testRebuildProjectionWithNonRebuildableProjectionAndUserRoot()
    {
        $this->artisan('lara-sauce:rebuild_projection', ['--force' => true, 'projection' => Reactors\UserReactor::class, 'roots' => Models\User\User::class]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }

    public function testRebuildProjectionWithUnusedProjectionAndWithoutRoot()
    {
        $this->artisan('lara-sauce:rebuild_projection', ['--force' => true, 'projection' => Projections\UnusedProjection::class]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }

    public function testRebuildProjectionWithUnusedProjectionAndWithUserRoot()
    {
        $this->artisan('lara-sauce:rebuild_projection', ['--force' => true, 'projection' => Projections\UnusedProjection::class, 'roots' => Models\User\User::class]);
        $this->assertEquals($this->expectedResult, json_encode($this->app['db']->table('command_test_users')->get()));
    }
}
