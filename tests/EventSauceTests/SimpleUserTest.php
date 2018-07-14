<?php

namespace Tests\EventSauceTests;

use APPelit\LaraSauce\Util\UuidGen;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;

class SimpleUserTest extends TestCase
{
    /**
     * @return Models\User\Types\UserIdType
     */
    protected function newAggregateRootId(): AggregateRootId
    {
        static $id;

        if (!$id) {
            $id = Models\User\Types\UserIdType::fromNative(UuidGen::generate(4)->toString());
        }

        return $id;
    }

    protected function aggregateRootClassName(): string
    {
        return Models\User\User::class;
    }

    protected function handle($command)
    {
        if ($command instanceof Models\User\Commands\RegisterUser) {
            $user = $this->repository->retrieve($this->newAggregateRootId());

            $user->performRegisterUser($command);

            $this->assertEquals([
                'email' => $user->getEmail()->toString(),
                'name' => $user->getName()->toString(),
                'password' => $user->getPassword()->toString(),
                'username' => $user->getUsername()->toString()
            ], [
                'email' => $command->email()->toString(),
                'name' => $command->name()->toString(),
                'password' => $command->password()->toString(),
                'username' => $command->username()->toString()
            ]);
        } else {
            /** @var Models\User\User $user */
            $user = $this->repository->retrieve($command->userId());

            if ($command instanceof Models\User\Commands\ChangeEmail) {
                $user->performChangeEmail($command);

                $this->assertEquals($user->getEmail()->toString(), $command->email()->toString());
            } else if ($command instanceof Models\User\Commands\ChangeName) {
                $user->performChangeName($command);

                $this->assertEquals($user->getName()->toString(), $command->name()->toString());
            } else if ($command instanceof Models\User\Commands\ChangePassword) {
                $user->performChangePassword($command);

                $this->assertEquals($user->getPassword()->toString(), $command->password()->toString());
            } else if ($command instanceof Models\User\Commands\ChangeUsername) {
                $user->performChangeUsername($command);

                $this->assertEquals($user->getUsername()->toString(), $command->username()->toString());
            }
        }

        $this->repository->persist($user);
    }

    /**
     * @return Consumer[]
     */
    protected function consumers(): array
    {
        if (!$this->app) {
            $this->refreshApplication();
        }

        return [
            $this->app->make(Projections\UserProjection::class),
            $this->app->make(Reactors\UserReactor::class),
            $this->app->make(Reactors\OrderedUserReactor::class),
        ];
    }

    public function testRegisterUser()
    {
        $this->when(
            new Models\User\Commands\RegisterUser(
                $name = Models\User\Types\Name::fromNative('test'),
                $email = Models\User\Types\Email::fromNative('test@example.com'),
                $username = Models\User\Types\Username::fromNative('test'),
                $password = Models\User\Types\Password::fromNative('password')
            )
        )->then(
            new Models\User\Events\UserRegistered(
                $name,
                $email,
                $username,
                $password
            )
        );
    }

    public function testChangeUsername()
    {
        $this->given(
            new Models\User\Events\UserRegistered(
                Models\User\Types\Name::fromNative('test'),
                Models\User\Types\Email::fromNative('test@example.com'),
                Models\User\Types\Username::fromNative('test'),
                Models\User\Types\Password::fromNative('password')
            )
        )->when(
            new Models\User\Commands\ChangeUsername(
                $this->newAggregateRootId(),
                $username = Models\User\Types\Username::fromNative('test2')
            )
        )->then(
            new Models\User\Events\UsernameChanged(
                $username
            )
        );
    }

    protected function aggregateRootRepository(string $className,
                                               MessageRepository $repository,
                                               MessageDispatcher $dispatcher,
                                               MessageDecorator $decorator): AggregateRootRepository
    {
        $this->messageRepository = $this->app->make(Repository\LaravelMessageRepository::class, ['tableName' => 'simple_user_test']);

        return parent::aggregateRootRepository($className, $this->messageRepository, $dispatcher, $decorator);
    }
}
