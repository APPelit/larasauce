<?php

namespace Tests\CommandTests;

use Tests\TestCase;

class GeneratesClassesTest extends TestCase
{
    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        if (file_exists(__DIR__ . '/GeneratedModels/Dummy/Commands')) {
            static::recursiveRemoveDir(__DIR__ . '/GeneratedModels/Dummy/Commands');
        }

        if (file_exists(__DIR__ . '/GeneratedModels/Dummy/Events')) {
            static::recursiveRemoveDir(__DIR__ . '/GeneratedModels/Dummy/Events');
        }

        if (file_exists(__DIR__ . '/GeneratedModels/User/Commands')) {
            static::recursiveRemoveDir(__DIR__ . '/GeneratedModels/User/Commands');
        }

        if (file_exists(__DIR__ . '/GeneratedModels/User/Events')) {
            static::recursiveRemoveDir(__DIR__ . '/GeneratedModels/User/Events');
        }
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
            'autogenerate' => true,
            'output_path' => realpath(__DIR__ . '/..'),
            'roots' => [
                GeneratedModels\User\User::class => [
                    'definition' => [
                        'namespace' => 'Tests\\CommandTests\\GeneratedModels\\User',
                        'commands' => [
                            'RegisterUser' => [
                                'fields' => [
                                    'name' => [
                                        'type' => '\\' . GeneratedModels\User\Types\NameType::class,
                                        'example' => "'John Doe'",
                                        'deserializer' => '{type}::fromPayload({param})',
                                        'serializer' => '{param}->toPayload()',
                                    ],
                                    'email' => 'string',
                                    'username' => [
                                        'type' => '\\' . GeneratedModels\User\Types\UsernameType::class,
                                    ],
                                    'password' => [
                                        'type' => '\\' . GeneratedModels\User\Types\PasswordType::class,
                                    ],
                                ]
                            ],
                        ],
                        'events' => [
                            'UserRegistered' => [
                                'fields' => [
                                    'name' => [
                                        'type' => '\\' . GeneratedModels\User\Types\NameType::class,
                                        'example' => "'John Doe'",
                                        'deserializer' => '{type}::fromPayload({param})',
                                        'serializer' => '{param}->toPayload()',
                                    ],
                                    'email' => 'string',
                                    'username' => [
                                        'type' => '\\' . GeneratedModels\User\Types\UsernameType::class,
                                    ],
                                    'password' => [
                                        'type' => '\\' . GeneratedModels\User\Types\PasswordType::class,
                                    ],
                                ]
                            ],
                        ],
                        'fields' => [
                            'email' => [
                                'type' => 'string',
                                'example' => "user@example.com",
                            ],
                            'userId' => [
                                'type' => 'userIdType'
                            ],
                            'password' => [
                                'type' => '\\' . GeneratedModels\User\Types\PasswordType::class,
                                'example' => "'example-password'",
                                'deserializer' => '{type}::fromPayload({param})',
                                'serializer' => '{param}->toPayload()',
                            ],
                            'username' => [
                                'type' => '\\' . GeneratedModels\User\Types\UsernameType::class,
                                'example' => "'example-user'",
                                'deserializer' => '{type}::fromPayload({param})',
                                'serializer' => '{param}->toPayload()',
                            ],
                        ],
                        'types' => [
                            'userIdType' => [
                                'type' => '\\' . GeneratedModels\User\Types\UserIdType::class,
                                'example' => "'1bec9969-77bf-48fa-a191-63736dbca3e5'",
                                'deserializer' => '{type}::fromPayload({param})',
                                'serializer' => '{param}->toPayload()',
                            ],
                        ],
                    ],
                ],
                GeneratedModels\Dummy\Dummy::class => [
                    'autogenerate' => false,
                    'definition' => [
                        'namespace' => 'Tests\\CommandTests\\GeneratedModels\\Dummy',
                        'commands' => [
                            'RegisterUser' => [
                                'fields' => [
                                    'name' => [
                                        'type' => '\\' . GeneratedModels\User\Types\NameType::class,
                                        'example' => "'John Doe'",
                                        'deserializer' => '{type}::fromPayload({param})',
                                        'serializer' => '{param}->toPayload()',
                                    ],
                                    'email' => 'string',
                                    'username' => [
                                        'type' => '\\' . GeneratedModels\User\Types\UsernameType::class,
                                    ],
                                    'password' => [
                                        'type' => '\\' . GeneratedModels\User\Types\PasswordType::class,
                                    ],
                                ]
                            ],
                        ],
                        'events' => [
                            'UserRegistered' => [
                                'fields' => [
                                    'name' => [
                                        'type' => '\\' . GeneratedModels\User\Types\NameType::class,
                                        'example' => "'John Doe'",
                                        'deserializer' => '{type}::fromPayload({param})',
                                        'serializer' => '{param}->toPayload()',
                                    ],
                                    'email' => 'string',
                                    'username' => [
                                        'type' => '\\' . GeneratedModels\User\Types\UsernameType::class,
                                    ],
                                    'password' => [
                                        'type' => '\\' . GeneratedModels\User\Types\PasswordType::class,
                                    ],
                                ]
                            ],
                        ],
                        'fields' => [
                            'email' => [
                                'type' => 'string',
                                'example' => "user@example.com",
                            ],
                            'userId' => [
                                'type' => 'userIdType'
                            ],
                            'password' => [
                                'type' => '\\' . GeneratedModels\User\Types\PasswordType::class,
                                'example' => "'example-password'",
                                'deserializer' => '{type}::fromPayload({param})',
                                'serializer' => '{param}->toPayload()',
                            ],
                            'username' => [
                                'type' => '\\' . GeneratedModels\User\Types\UsernameType::class,
                                'example' => "'example-user'",
                                'deserializer' => '{type}::fromPayload({param})',
                                'serializer' => '{param}->toPayload()',
                            ],
                        ],
                        'types' => [
                            'userIdType' => [
                                'type' => '\\' . GeneratedModels\User\Types\UserIdType::class,
                                'example' => "'1bec9969-77bf-48fa-a191-63736dbca3e5'",
                                'deserializer' => '{type}::fromPayload({param})',
                                'serializer' => '{param}->toPayload()',
                            ],
                        ],
                    ],
                ]
            ],
            'serializers' => [
                \EventSauce\EventSourcing\AggregateRootId::class => '{param}->toString()'
            ],
            'deserializers' => [
                \EventSauce\EventSourcing\AggregateRootId::class => '{type}::fromString({param})'
            ],
        ]);
    }

    public function testDummyIsIgnored()
    {
        $this->artisan('lara-sauce:generate_classes', ['--force' => true]);

        $this->assertDirectoryNotExists(__DIR__ . '/GeneratedModels/Dummy/Commands');
        $this->assertDirectoryNotExists(__DIR__ . '/GeneratedModels/Dummy/Events');
    }

    public function testUserNamespacesAreCreated()
    {
        $this->assertDirectoryExists(__DIR__ . '/GeneratedModels/User/Commands');
        $this->assertDirectoryExists(__DIR__ . '/GeneratedModels/User/Events');
    }

    public function testCorrectCommandFilesAreCreated()
    {
        $this->assertEquals(['.', '..', 'RegisterUser.php'], scandir(__DIR__ . '/GeneratedModels/User/Commands'));
    }

    public function testCorrectEventFilesAreCreated()
    {
        $this->assertEquals(['.', '..', 'UserRegistered.php'], scandir(__DIR__ . '/GeneratedModels/User/Events'));
    }

    public function testRegisterUserContentsIsCorrect()
    {
        $this->assertFileEquals(__DIR__ . '/ExpectedGeneratedModels/Commands/RegisterUser.php', __DIR__ . '/GeneratedModels/User/Commands/RegisterUser.php');
    }

    public function testUserRegisteredContentsIsCorrect()
    {
        $this->assertFileEquals(__DIR__ . '/ExpectedGeneratedModels/Events/UserRegistered.php', __DIR__ . '/GeneratedModels/User/Events/UserRegistered.php');
    }
}
