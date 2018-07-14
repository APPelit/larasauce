<?php

namespace Tests\CommandTests\Models\User;

use EventSauce\EventSourcing\BaseAggregateRoot;
use Tests\CommandTests\Models\User\Types\Email;
use Tests\CommandTests\Models\User\Types\Name;
use Tests\CommandTests\Models\User\Types\Password;
use Tests\CommandTests\Models\User\Types\UserIdType;
use Tests\CommandTests\Models\User\Types\Username;

/**
 * Class User
 * @package Tests\CommandTests\Models\User
 *
 * @access protected
 * @method void recordThat(\EventSauce\EventSourcing\Serialization\SerializableEvent $event)
 */
class User extends BaseAggregateRoot
{
    /** @var Name */
    private $name;

    /** @var Username */
    private $username;

    /** @var Email */
    private $email;

    /** @var Password */
    private $password;

    /**
     * User constructor.
     * @param UserIdType $aggregateRootId
     */
    public function __construct(UserIdType $aggregateRootId)
    {
        parent::__construct($aggregateRootId);

        $this->name = null;
        $this->username = null;
        $this->email = null;
        $this->password = null;
    }

    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @return Name|null
     */
    public function getName(): ?Name
    {
        return $this->name;
    }

    /**
     * @return Password|null
     */
    public function getPassword(): ?Password
    {
        return $this->password;
    }

    /**
     * @return Username|null
     */
    public function getUsername(): ?Username
    {
        return $this->username;
    }

    /**
     * @param Commands\RegisterUser $registerUser
     * @return User
     */
    public function performRegisterUser(Commands\RegisterUser $registerUser): User
    {
        $this->recordThat(new Events\UserRegistered(
            $registerUser->name(),
            $registerUser->email(),
            $registerUser->username(),
            $registerUser->password()
        ));

        return $this;
    }

    /**
     * @param Commands\ChangeEmail $changeEmail
     * @return User
     */
    public function performChangeEmail(Commands\ChangeEmail $changeEmail): User
    {
        $this->recordThat(new Events\EmailChanged(
            $changeEmail->email()
        ));

        return $this;
    }

    /**
     * @param Commands\ChangeName $changeName
     * @return User
     */
    public function performChangeName(Commands\ChangeName $changeName): User
    {
        $this->recordThat(new Events\NameChanged(
            $changeName->name()
        ));

        return $this;
    }

    /**
     * @param Commands\ChangePassword $changePassword
     * @return User
     */
    public function performChangePassword(Commands\ChangePassword $changePassword): User
    {
        $this->recordThat(new Events\PasswordChanged(
            $changePassword->password()
        ));

        return $this;
    }

    /**
     * @param Commands\ChangeUsername $changeUsername
     * @return User
     */
    public function performChangeUsername(Commands\ChangeUsername $changeUsername): User
    {
        $this->recordThat(new Events\UsernameChanged(
            $changeUsername->username()
        ));

        return $this;
    }

    /**
     * @param Events\EmailChanged $emailChanged
     */
    protected function applyEmailChanged(Events\EmailChanged $emailChanged)
    {
        $this->email = $emailChanged->email();
    }

    /**
     * @param Events\NameChanged $nameChanged
     */
    protected function applyNameChanged(Events\NameChanged $nameChanged)
    {
        $this->name = $nameChanged->name();
    }

    /**
     * @param Events\PasswordChanged $passwordChanged
     */
    protected function applyPasswordChanged(Events\PasswordChanged $passwordChanged)
    {
        $this->password = $passwordChanged->password();
    }

    /**
     * @param Events\UserRegistered $userRegistered
     */
    protected function applyUserRegistered(Events\UserRegistered $userRegistered)
    {
        $this->email = $userRegistered->email();
        $this->name = $userRegistered->name();
        $this->password = $userRegistered->password();
        $this->username = $userRegistered->username();
    }

    /**
     * @param Events\UsernameChanged $usernameChanged
     */
    protected function applyUsernameChanged(Events\UsernameChanged $usernameChanged)
    {
        $this->username = $usernameChanged->username();
    }
}
