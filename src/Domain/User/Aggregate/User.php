<?php

namespace Leos\Domain\User\Aggregate;

use Leos\Domain\User\Event\UserEmailWasChanged;
use Leos\Domain\User\Event\UserWasCreated;
use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;

use Leos\Domain\User\ValueObject\Username;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class User extends AggregateRoot
{
    /**
     * @var UserId
     */
    private $uuid;

    /**
     * @var Username
     */
    private $username;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var \DateTimeImmutable
     */
    private $signUpAt;

    protected function __construct()
    {
        $this->signUpAt = new \DateTimeImmutable();
    }

    public static function create(UserId $userId, Username $username, Email $email): self
    {
        $user = new self();

        $user->recordThat(
            UserWasCreated::with($userId, $username, $email)
        );

        return $user;
    }

    public function changeEmail(Email $newEmail)
    {
        $this->recordThat(
            UserEmailWasChanged::with($this->uuid, $newEmail)
        );
    }

    public function uuid(): UserId
    {
        return $this->uuid;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function signedUpAt(): \DateTimeImmutable
    {
        return $this->signUpAt;
    }

    public function whenUserWasCreated(UserWasCreated $userWasCreated): void
    {
        $this->uuid = UserId::fromString($userWasCreated->aggregateId());

        $this->setEmail($userWasCreated->email());
        $this->setUsername($userWasCreated->username());
    }

    public function whenUserEmailWasChanged(UserEmailWasChanged $userEmailWasChanged): void
    {
        $this->setEmail($userEmailWasChanged->email());
    }

    private function setUsername(Username $username): void
    {
        $this->username = $username;
    }

    private function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    protected function aggregateId(): string
    {
        return $this->uuid->toString();
    }

    /**
     * @param AggregateChanged|UserWasCreated|UserEmailWasChanged $event
     */
    protected function apply(AggregateChanged $event): void
    {
        $eventType = get_class($event);

        switch ($eventType) {

            case UserWasCreated::class:

                $this->whenUserWasCreated($event);
                break;
            case UserEmailWasChanged::class:

                $this->whenUserEmailWasChanged($event);
                break;
            default:

                throw new \RuntimeException(sprintf(
                    'No handler for event %s in aggregate root %s',
                    $eventType,
                    get_class($this)
                ));
        }
    }

    public static function fromEvents(\Iterator $events): self
    {
        /** @var User $user */
        $user = self::reconstituteFromHistory($events);

        return $user;
    }
}
